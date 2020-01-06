const NewBookForm = function () {
    const newBook = document.getElementById('bookshelf-new-book');
    const index = newBook.getAttribute('data-index-path');

    const init = function () {
        const addAuthorButton = document.getElementById('bookshelf-new-book-addAuthor');
        if (addAuthorButton) {
            addAuthorButton.addEventListener('click', function (e) {
                e.preventDefault();
                Authors.addAuthor();
            });
        }
        if (newBook) {
            newBook.addEventListener('submit', function (e) { createBook(e); });
        }
    };

    const createBook = function (e) {
        e.preventDefault();
        const form = e.target;
        const path = form.getAttribute('action');
        const book = Book.create(e.target.elements);
        ErrorsHandler.reset(form);
        doCreateBook(book, path)
            .then(function (book) {
                console.log(book);
                ModalWindow.init(createBookNotification(book));
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
                    resolve(book);
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

    const createBookNotification = function (book) {
        const content = document.createDocumentFragment();
        const div = document.createElement('div');
        const paragraph = document.createElement('p');
        paragraph.textContent = 'Książka "' + book.title + '" została dodana do księgozbioru.';
        div.appendChild(paragraph);
        content.appendChild(div);
        const buttonsGroup = document.createElement('div');
        buttonsGroup.setAttribute('class', 'form-group text-center');
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Dodaj kolejną';
        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            newBook.reset();
            ModalWindow.closeModal();
        });
        buttonsGroup.appendChild(cancelButton);
        const indexButton = document.createElement('button');
        indexButton.setAttribute('class', 'btn btn-default action-button');
        indexButton.textContent = 'Pokaż książki';
        indexButton.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = index;
        });
        buttonsGroup.appendChild(indexButton);
        content.appendChild(buttonsGroup);
        return content;
    };

    return {
        init: init
    }
}();
NewBookForm.init();
