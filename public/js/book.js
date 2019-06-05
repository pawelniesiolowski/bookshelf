const Book = function () {

    const create = function (e, onSuccessCallback) {
        e.preventDefault();
        const newBookForm = e.target;
        const book = {
            authors: [],
            title: newBookForm.elements.namedItem('title').value,
            copies: newBookForm.elements.namedItem('copies').value,
            ISBN: newBookForm.elements.namedItem('isbn').value,
            price: newBookForm.elements.namedItem('price').value,
        };

        const author = {
            name: newBookForm.elements.namedItem('authorName').value,
            surname: newBookForm.elements.namedItem('authorSurname').value,
        };
        
        if (author.name !== '' || author.surname !== '') {
            book.authors.push(author);
        }

        const path = newBookForm.getAttribute('action') ;

        makePostRequest(book, path, onSuccessCallback);
    };

    const edit = function(e, onSuccessCallback) {
        const getBookPath = e.target.getAttribute('data-get-book-path');
        const editBookPath = e.target.getAttribute('data-edit-book-path');

        makeGetRequest(getBookPath, function (book) { createEditDiv(book, editBookPath, onSuccessCallback) })
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

        const saveButton = document.createElement('button');
        saveButton.setAttribute('class', 'btn btn-success action-button');
        saveButton.setAttribute('type', 'submit');
        saveButton.textContent = 'Zapisz';
        form.appendChild(saveButton);
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Anuluj';
        cancelButton.addEventListener('click', function (e) { e.preventDefault(); ModalWindow.closeModal(); });
        form.appendChild(cancelButton);
        form.addEventListener('submit', function (e) { 
            ModalWindow.closeModal(); 
            const data = createBookData(e.target.elements)
            makePutRequest(data, path, onSuccessCallback);
        });
        div.appendChild(form);
        ModalWindow.init(div);
    };

    const createBookData = function(form) {
        const book = {
            authors: [],
            title: form.namedItem('title').value,
            ISBN: form.namedItem('ISBN').value,
            price: form.namedItem('price').value,
        };

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

    return {
        create: create,
        edit: edit
    };
}();
