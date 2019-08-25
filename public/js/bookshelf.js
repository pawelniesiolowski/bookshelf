const Bookshelf = function () {

    const bookshelfTable = document.getElementById('bookshelf-table-body');
    const newBook = document.getElementById('bookshelf-new-book');
    const paths = {
        index: bookshelfTable.getAttribute('data-index'),
        receive: bookshelfTable.getAttribute('data-receive-path'),
        release: bookshelfTable.getAttribute('data-release-path'),
        sell: bookshelfTable.getAttribute('data-sell-path'),
        edit: bookshelfTable.getAttribute('data-edit-book-path'),
        get: bookshelfTable.getAttribute('data-get-book-path'),
        deleteBook: bookshelfTable.getAttribute('data-delete-book-path'),
        getReceivers: bookshelfTable.getAttribute('data-get-receivers-path')
    }
    
    const init = function () {
        bookshelfTable.addEventListener('click', function (e) { checkEvent(e); });
        newBook.addEventListener('submit', function (e) { createBook(e); });
        loadBooks();
        const addAuthorButton = document.getElementById('bookshelf-new-book-addAuthor');
        addAuthorButton.addEventListener('click', function (e) {
            e.preventDefault();
            Authors.addAuthor();
        });
    };

    const checkEvent = function (e) {
        const bookId = e.target.parentElement.getAttribute('bookId');

        if (e.target.classList.contains('receive-button')) {
            receiveBook(bookId);
        } else if (e.target.classList.contains('release-button')) {
            releaseBook(bookId);
        } else if (e.target.classList.contains('sell-button')) {
            sellBook(bookId);
        } else if (e.target.classList.contains('edit-book-button')) {
            editBook(bookId);
        } else if (e.target.classList.contains('delete-book-button')) {
            deleteBook(bookId);
        }
    };

    const createPath = function (path, id) {
        return path.replace('0', id);
    };

    const loadBooks = function () {
        fetchBooks(paths.index)
            .then(decode)
            .then(function(data) {
                const content = BookshelfElements.tableBodyContent(data.books);
                reloadBookshelfTable(content);
            })
            .catch(function(error) {
                console.log(error);
            });
    };


    const reloadBookshelfTable = function (newContent) {
        while (bookshelfTable.firstChild) {
            bookshelfTable.removeChild(bookshelfTable.firstChild);
        }
        bookshelfTable.appendChild(newContent);
    };

    const createBook = function (e) {
        e.preventDefault();
        const form = e.target;
        const path = form.getAttribute('action');
        const book = Book.create(e.target.elements);
        ErrorsHandler.reset(form);
        doCreateBook(book, path)
            .then(function () {
                loadBooks();
            })
            .catch(function (errors) {
                ErrorsHandler.show(form, JSON.parse(errors).errors);
            });
    };

    const doCreateBook = function (book, path) {
        return new Promise(function (resolve, reject) {
            const request = new XMLHttpRequest();
            request.open('POST', path, true);
            request.onload = function () {
                if (request.status === 201) {
                    resolve();
                } else {
                    reject(this.response);
                }
            };
            request.onerror = function () {
                reject(Error('Błąd! Nie udało się dodać książki'));
            };
            request.send(JSON.stringify(book));
        });
    };

    const receiveBook = function (id) {
        const getPath = createPath(paths.get, id);
        const receivePath = createPath(paths.receive, id);

        fetchBooks(getPath)
            .then(decode)
            .then(function (data) {
                const form = BookshelfElements.simpleBookActionForm(data.book);
                form.addEventListener('submit', function (e) { 
                    e.preventDefault();
                    const data = {
                        copies: e.target.elements.namedItem('copies').value,
                    };
                    emitBookChangeEvent(data, receivePath, form);
                });
                const div = BookshelfElements.simpleBookActionDiv(data.book, 'dodać');
                div.appendChild(form);
                ModalWindow.init(div);
            })
            .catch(function (error) {
                console.log(error);
            });
    };

    const releaseBook = function (id) {
        const getPath = createPath(paths.get, id);
        const getReceiversPath = createPath(paths.getReceivers, id);
        const releasePath = createPath(paths.release, id);

        fetchBooks(getPath)
            .then(decode)
            .then(function (booksData) {
                return fetchReceivers(getReceiversPath)
                    .then(decode)
                    .then(function (receiversData) {
                        return {
                            book: booksData.book,
                            receivers: receiversData.receivers
                        }
                    });
            })
            .then(function (allData) {
                doReleaseBook(allData, releasePath);
            })
            .catch(function (error) {
                console.log(error);
            });
    };

    const doReleaseBook = function (data, path) {
        const div = document.createElement('div');
        const info = BookshelfElements.releaseDiv(data.book);
        div.appendChild(info);
        const form = BookshelfElements.releaseForm(data.receivers);
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const data = {
                copies: e.target.elements.namedItem('copies').value,
                receiver: e.target.elements.namedItem('receiver').value,
                comment: e.target.elements.namedItem('comment').value,
            };
            emitBookChangeEvent(data, path, form);
        });
        div.appendChild(form);
        ModalWindow.init(div);
    };

    const sellBook = function (id) {
        const getPath = createPath(paths.get, id);
        const sellPath = createPath(paths.sell, id);

        fetchBooks(getPath)
            .then(decode)
            .then(function (booksData) {
                doSellBook(booksData, sellPath);
            })
            .catch(function (error) {
                console.log(error);
            });
    };

    const doSellBook = function (data, sellPath) {
        const form = BookshelfElements.simpleBookActionForm(data.book);
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const data = {
                copies: e.target.elements.namedItem('copies').value,
            };
            emitBookChangeEvent(data, sellPath, form);
        });
        const div = BookshelfElements.simpleBookActionDiv(data.book, 'sprzedać');
        div.appendChild(form);
        ModalWindow.init(div);
    };

    const deleteBook = function (id) {
        const getPath = createPath(paths.get, id);
        const deletePath = createPath(paths.deleteBook, id);

        fetchBooks(getPath)
            .then(decode)
            .then(function (data) {
                const displayedAuthors = Authors.createDisplayedAuthors(data.book.authors);
                const text = 'Czy na pewno chcesz usunąć książkę: ' + displayedAuthors + ' "' + data.book.title + '"?';
                const approvalDiv = BookshelfElements.deleteDiv(text, function () {
                    ModalWindow.closeModal(); 
                    doDeleteBook(deletePath)
                        .then(loadBooks)
                        .catch(function (errors) {
                            console.log(errors);
                        });
                    
                });
                ModalWindow.init(approvalDiv);
            });
    };

    const doDeleteBook = function (path) {
        return fetch(path, {
            method: 'DELETE'
        });
    };

    const editBook = function (id) {
        const getPath = createPath(paths.get, id);
        const editPath = createPath(paths.edit, id);

        fetchBooks(getPath)
            .then(decode)
            .then(function (data) {
                const div = document.createElement('div');
                const text = document.createElement('h2');
                text.setAttribute('class', 'text-center');
                text.textContent = 'Edycja książki';
                div.appendChild(text);
                const form = BookshelfElements.editForm(data.book);
                form.addEventListener('submit', function (e) { 
                    e.preventDefault();
                    ErrorsHandler.reset(form);
                    const data = Book.create(e.target.elements)
                    doEditBook(data, editPath)
                        .then(function () {
                            ModalWindow.closeModal();
                            loadBooks();
                        })
                        .catch(function (errors) {
                            ErrorsHandler.show(form, JSON.parse(errors).errors);
                        });
                });
                div.appendChild(form);
                ModalWindow.init(div);
            });

    };

    const doEditBook = function (data, path) {
        return new Promise(function (resolve, reject) {
            const request = new XMLHttpRequest();
            request.open('PUT', path, true);
            request.onload = function () {
                if (request.status === 204) {
                    resolve();
                } else {
                    reject(this.response);
                }
            };
            request.onerror = function () {
                reject(Error('Błąd! Nie udało się wykonać akcji'));
            };
            request.send(JSON.stringify(data));
        });
    };

    const emitBookChangeEvent = function (data, path, form) {
        ErrorsHandler.reset(form);
        doEmitBookChangeEvent(data, path)
            .then(function () {
                ModalWindow.closeModal();
                loadBooks();
            })
            .catch(function (errors) {
                ErrorsHandler.show(form, JSON.parse(errors).errors);
            });
    };

    const doEmitBookChangeEvent = function (data, path) {
        return new Promise(function (resolve, reject) {
            const request = new XMLHttpRequest();
            request.open('POST', path, true);
            request.onload = function () {
                if (request.status === 204) {
                    resolve();
                } else {
                    reject(this.response);
                }
            };
            request.onerror = function () {
                reject(Error('Błąd! Nie udało się wykonać akcji'));
            };
            request.send(JSON.stringify(data));
        });
    };

    const decode = function (response) {
        return response.json();
    };

    const fetchBooks = function (path) {
        return fetch(path);
    };

    const fetchReceivers = function (path) {
        return fetch(path);
    };

    return {
        init: init
    };
}();

Bookshelf.init();
