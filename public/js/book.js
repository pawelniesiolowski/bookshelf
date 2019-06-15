const Book = function () {

    const create = function (e, onSuccessCallback) {
        e.preventDefault();
        const book = createBookData(e.target.elements);
        const path = e.target.getAttribute('action') ;
        makePostRequest(book, path, onSuccessCallback);
    };

    const edit = function(editBooksPath, getBookPath) {
        makeGetRequest(getBookPath, function (book) { BookshelfElements.editDiv(book, editBooksPath, createBookData, makePutRequest); })
    };

    const deleteBook = function(deleteBooksPath, getBookPath) {
        makeGetRequest(getBookPath, function (book) { BookshelfElements.deleteDiv(book, deleteBooksPath, makeDeleteRequest); });
    };

    const receive = function (receiveBookPath, getBookPath) {
        makeGetRequest(getBookPath, function (book) { BookshelfElements.receiveDiv(book, receiveBookPath, emitBookChangeEvent); });
    };

    const release = function (releaseBookPath, getBookPath, getReceiversPath) {
        makeGetRequest(getBookPath, function (book) { 
            makeGetRequest(getReceiversPath, function (receivers) {
                BookshelfElements.releaseDiv(book, receivers, releaseBookPath, emitBookChangeEvent);
            });
        });
    };

    const makeGetRequest = function (path, onSuccessCallback) {
        const request = new XMLHttpRequest();
        request.open('GET', path);
        request.onload = function () {
            let response = {};
            if (request.status === 200) {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('book')) {
                    onSuccessCallback(response.book);
                } else if (response.hasOwnProperty('receivers')) {
                    onSuccessCallback(response.receivers);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się wykonać akcji');
        };
        request.send();
    };

    const makePutRequest = function (data, path) {
        const request = new XMLHttpRequest();
        request.open('PUT', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 204) {
                BookshelfActions.loadBooks();
            } else {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('errors')) {
                    console.log(response.errors);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się dodać książki');
        };
        request.send(JSON.stringify(data));
    };

    const makePostRequest = function (book, path, onSuccessCallback) {
        const request = new XMLHttpRequest();
        request.open('POST', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 201) {
                BookshelfActions.loadBooks();
            } else {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('errors')) {
                    console.log(response.errors);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się dodać książki');
        };
        request.send(JSON.stringify(book));
    };

    const makeDeleteRequest = function (path, onSuccessCallback) {
        const request = new XMLHttpRequest();
        request.open('DELETE', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 200) {
                BookshelfActions.loadBooks();
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się usunąć książki');
        };
        request.send();
    };

    const createBookData = function(form) {
        const book = {
            authors: [],
            title: form.namedItem('title').value,
            ISBN: form.namedItem('ISBN').value,
            price: form.namedItem('price').value,
        };

        if (copies = form.namedItem('copies')) {
            book.copies = copies.value;
        }

        const author = {
            name: form.namedItem('authorName').value,
            surname: form.namedItem('authorSurname').value,
        };
        
        if (author.name !== '' || author.surname !== '') {
            book.authors.push(author);
        }

        return book;
    };

    const emitBookChangeEvent = function (data, path) {
        const request = new XMLHttpRequest();
        request.open('POST', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 204) {
                BookshelfActions.loadBooks();
            } else {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('errors')) {
                    console.log(response.errors);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się wykonać akcji');
        };
        request.send(JSON.stringify(data));
    };

    return {
        create: create,
        edit: edit,
        deleteBook: deleteBook,
        receive: receive,
        release: release
    };
}();
