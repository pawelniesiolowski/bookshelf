const Book = function () {

    const create = function (e, onSuccessCallback) {
        e.preventDefault();
        const book = createBookData(e.target.elements);
        const path = e.target.getAttribute('action') ;
        makePostRequest(book, path, onSuccessCallback);
    };

    const edit = function(e, onSuccessCallback) {
        const getBookPath = e.target.getAttribute('data-get-book-path');
        const editBookPath = e.target.getAttribute('data-edit-book-path');

        makeGetRequest(getBookPath, function (book) { createEditDiv(book, editBookPath, onSuccessCallback); })
    };

    const deleteBook = function(e, onSuccessCallback) {
        const deleteBookPath = e.target.getAttribute('data-delete-book-path');
        const getBookPath = e.target.getAttribute('data-get-book-path');

        makeGetRequest(getBookPath, function (book) { createDeleteDiv(book, deleteBookPath, onSuccessCallback); });
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
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się wykonać akcji');
        };
        request.send();
    };

    const createEditDiv = function (book, path, onSuccessCallback) {
        const div = document.createElement('div');
        const text = document.createElement('h2');
        text.setAttribute('class', 'text-center');
        text.textContent = 'Edycja książki';
        div.appendChild(text);

        const form = document.createElement('form');

        let author = ['', ''];
        if (book.author !== '') {
            author = book.author.split(' ');
        }

        const authorGroup = document.createElement('div');
        authorGroup.setAttribute('class', 'form-group');
        const surnameLabel = document.createElement('label');
        surnameLabel.setAttribute('for', 'book-edit-form-author-surname');
        surnameLabel.textContent = 'Nazwisko autora';
        const surnameInput = document.createElement('input');
        surnameInput.setAttribute('type', 'text');
        surnameInput.setAttribute('name', 'authorSurname');
        surnameInput.setAttribute('id', 'book-edit-form-author-surname');
        surnameInput.setAttribute('class', 'form-control');
        surnameInput.setAttribute('value', author[0]);
        authorGroup.appendChild(surnameLabel);
        authorGroup.appendChild(surnameInput);

        const nameLabel = document.createElement('label');
        nameLabel.setAttribute('for', 'book-edit-form-author-name');
        nameLabel.textContent = 'Imię autora';
        const nameInput = document.createElement('input');
        nameInput.setAttribute('type', 'text');
        nameInput.setAttribute('name', 'authorName');
        nameInput.setAttribute('id', 'book-edit-form-author-name');
        nameInput.setAttribute('class', 'form-control');
        nameInput.setAttribute('value', author[1]);
        authorGroup.appendChild(nameLabel);
        authorGroup.appendChild(nameInput);
        form.appendChild(authorGroup);
        
        const bookGroup = document.createElement('div');
        bookGroup.setAttribute('class', 'form-group');

        const titleLabel = document.createElement('label');
        titleLabel.setAttribute('for', 'book-edit-form-title');
        titleLabel.textContent = 'Tytuł';
        const title = document.createElement('input');
        title.setAttribute('type', 'text');
        title.setAttribute('name', 'title');
        title.setAttribute('id', 'book-edit-form-title');
        title.setAttribute('class', 'form-control');
        title.setAttribute('value', book.title);
        bookGroup.appendChild(titleLabel);
        bookGroup.appendChild(title);

        const isbnLabel = document.createElement('label');
        isbnLabel.setAttribute('for', 'book-edit-form-isbn');
        isbnLabel.textContent = 'ISBN';
        const isbnInput = document.createElement('input');
        isbnInput.setAttribute('type', 'text');
        isbnInput.setAttribute('name', 'ISBN');
        isbnInput.setAttribute('id', 'book-edit-form-isbn');
        isbnInput.setAttribute('class', 'form-control');
        isbnInput.setAttribute('value', book.ISBN);
        bookGroup.appendChild(isbnLabel);
        bookGroup.appendChild(isbnInput);

        const priceLabel = document.createElement('label');
        priceLabel.setAttribute('for', 'book-edit-form-price');
        priceLabel.textContent = 'Cena';
        const priceInput = document.createElement('input');
        priceInput.setAttribute('type', 'number');
        priceInput.setAttribute('name', 'price');
        priceInput.setAttribute('id', 'book-edit-form-price');
        priceInput.setAttribute('class', 'form-control');
        priceInput.setAttribute('value', book.price);
        bookGroup.appendChild(priceLabel);
        bookGroup.appendChild(priceInput);
        form.appendChild(bookGroup);


        const buttonsGroup = document.createElement('div');
        buttonsGroup.setAttribute('class', 'form-group text-center');
        const saveButton = document.createElement('button');
        saveButton.setAttribute('class', 'btn btn-success action-button');
        saveButton.setAttribute('type', 'submit');
        saveButton.textContent = 'Zapisz';
        buttonsGroup.appendChild(saveButton);
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Anuluj';
        cancelButton.addEventListener('click', function (e) { e.preventDefault(); ModalWindow.closeModal(); });
        buttonsGroup.appendChild(cancelButton);
        form.appendChild(buttonsGroup);
        form.addEventListener('submit', function (e) { 
            ModalWindow.closeModal(); 
            const data = createBookData(e.target.elements)
            makePutRequest(data, path, onSuccessCallback);
        });
        div.appendChild(form);
        ModalWindow.init(div);
    };

    const createDeleteDiv = function(book, deleteBookPath, onSuccessCallback) {
        const container = document.createElement('div');
        container.setAttribute('class', 'container');

        const rowDiv1 = document.createElement('div');
        rowDiv1.setAttribute('class', 'row');
        const colDiv1 = document.createElement('div');
        colDiv1.setAttribute('class', 'col text-center');
        const text = document.createElement('p');
        text.textContent = 'Czy na pewno chcesz usunąć książkę: ';
        colDiv1.appendChild(text);
        const bookText = document.createElement('h2');
        bookText.textContent = book.author + ' "' + book.title + '"?';
        colDiv1.appendChild(bookText);
        rowDiv1.appendChild(colDiv1);
        container.appendChild(rowDiv1);

        const rowDiv2 = document.createElement('div');
        rowDiv2.setAttribute('class', 'row mt-3');
        const colDiv2 = document.createElement('div');
        colDiv2.setAttribute('class', 'col text-center');
        const deleteButton = document.createElement('button');
        deleteButton.setAttribute('class', 'btn btn-danger action-button');
        deleteButton.textContent = 'Usuń';
        deleteButton.addEventListener('click', function (e) { makeDeleteRequest(deleteBookPath, onSuccessCallback); ModalWindow.closeModal(); });
        colDiv2.appendChild(deleteButton);
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Anuluj';
        cancelButton.addEventListener('click', function (e) { e.preventDefault(); ModalWindow.closeModal(); });
        colDiv2.appendChild(cancelButton);
        rowDiv2.appendChild(colDiv2);
        container.appendChild(rowDiv2);

        ModalWindow.init(container);
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

    const makePutRequest = function (data, path, onSuccessCallback) {
        const request = new XMLHttpRequest();
        request.open('PUT', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 204) {
                onSuccessCallback();
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
                onSuccessCallback();
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
                onSuccessCallback();
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się usunąć książki');
        };
        request.send();
    };

    return {
        create: create,
        edit: edit,
        deleteBook: deleteBook
    };
}();
