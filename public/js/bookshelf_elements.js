const BookshelfElements = function () {
    const tableBodyContent = function (books) {
        const content = document.createDocumentFragment();
        for (let book of books) {
            const tr = document.createElement('tr');
            const td1 = document.createElement('td');
            td1.textContent = book.author.surname + ' ' + book.author.name;
            tr.appendChild(td1);
            const td2 = document.createElement('td');
            td2.textContent = book.title;
            tr.appendChild(td2);
            const td3 = document.createElement('td');
            td3.textContent = book.copies;
            tr.appendChild(td3);
            const td4 = document.createElement('td');
            td4.textContent = book.ISBN;
            tr.appendChild(td4);
            const td5 = document.createElement('td');
            td5.textContent = book.price;
            tr.appendChild(td5);
            
            const td6 = document.createElement('td');
            td6.setAttribute('bookId', book.id);
            
            const receiveButton = document.createElement('button');
            receiveButton.setAttribute('class', 'receive-button action-button btn btn-success');
            receiveButton.textContent = 'Dodaj';
            
            const releaseButton = document.createElement('button');
            releaseButton.setAttribute('class', 'release-button action-button btn btn-warning');
            releaseButton.textContent = 'Wydaj';
            
            const sellButton = document.createElement('button');
            sellButton.setAttribute('class', 'sell-button action-button btn btn-info');
            sellButton.textContent = 'Sprzedaj';

            const editBookButton = document.createElement('button');
            editBookButton.setAttribute('class', 'edit-book-button action-button btn btn-secondary');
            editBookButton.textContent = 'Edytuj';
            
            const deleteBookButton = document.createElement('button');
            deleteBookButton.setAttribute('class', 'delete-book-button action-button btn btn-danger');
            deleteBookButton.textContent = 'Usuń';

            td6.appendChild(receiveButton);
            td6.appendChild(releaseButton);
            td6.appendChild(sellButton);
            td6.appendChild(editBookButton);
            td6.appendChild(deleteBookButton);
            tr.appendChild(td6);
            
            content.appendChild(tr);
        }

        return content;
    };

    const simpleBookActionDiv = function (book, verb) {
        const div = document.createElement('div');
        const text = document.createElement('p');
        text.textContent = 'Ile egzemplarzy książki "' + book.title + '" chcesz ' + verb + '?';
        div.appendChild(text);
        return div;
    };

    const simpleBookActionForm = function (book) {
        const form = document.createElement('form');
        const group = document.createElement('div');
        group.setAttribute('class', 'form-group');
        form.appendChild(group);
        const input = document.createElement('input');
        input.setAttribute('type', 'number');
        input.setAttribute('name', 'copies');
        input.setAttribute('class', 'form-control');
        group.appendChild(input);
        const div = document.createElement('div');
        div.setAttribute('class', 'text-center');
        form.appendChild(div);
        const saveButton = document.createElement('button');
        saveButton.setAttribute('class', 'btn btn-success action-button');
        saveButton.setAttribute('type', 'submit');
        saveButton.textContent = 'Zapisz';
        div.appendChild(saveButton);
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Anuluj';
        cancelButton.addEventListener('click', function (e) { e.preventDefault(); ModalWindow.closeModal(); });
        div.appendChild(cancelButton);
        return form;
    };

    const deleteDiv = function (textContent, deleteFunction) {
        const container = document.createElement('div');
        container.setAttribute('class', 'container');

        const rowDiv1 = document.createElement('div');
        rowDiv1.setAttribute('class', 'row');
        const colDiv1 = document.createElement('div');
        colDiv1.setAttribute('class', 'col text-center');
        const text = document.createElement('p');
        text.textContent = textContent;
        colDiv1.appendChild(text);
        rowDiv1.appendChild(colDiv1);
        container.appendChild(rowDiv1);

        const rowDiv2 = document.createElement('div');
        rowDiv2.setAttribute('class', 'row mt-3');
        const colDiv2 = document.createElement('div');
        colDiv2.setAttribute('class', 'col text-center');
        const deleteButton = document.createElement('button');
        deleteButton.setAttribute('class', 'btn btn-danger action-button');
        deleteButton.textContent = 'Usuń';
        deleteButton.addEventListener('click', function (e) { deleteFunction(); });
        colDiv2.appendChild(deleteButton);
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Anuluj';
        cancelButton.addEventListener('click', function (e) { e.preventDefault(); ModalWindow.closeModal(); });
        colDiv2.appendChild(cancelButton);
        rowDiv2.appendChild(colDiv2);
        container.appendChild(rowDiv2);
        return container;
    };

    const editForm = function (book) {

        const form = document.createElement('form');

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
        surnameInput.setAttribute('value', book.author.surname);
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
        nameInput.setAttribute('value', book.author.name);
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
        return form;
    };

    const releaseDiv = function (book) {
        const div = document.createElement('div');
        const text = document.createElement('h2');
        text.setAttribute('class', 'text-center');
        text.textContent = 'Wydajesz książkę: ' + book.author.name + ' ' + book.author.surname
            + ' "' + book.title + '"';
        div.appendChild(text);
        return div;
    };

    const releaseForm = function (receivers) {
        const form = document.createElement('form');

        const copiesGroup = document.createElement('div');
        copiesGroup.setAttribute('class', 'form-group');
        const copiesLabel = document.createElement('label');
        copiesLabel.setAttribute('for', 'book-release-form-copies');
        copiesLabel.textContent = 'Egzemplarze';
        const copiesInput = document.createElement('input');
        copiesInput.setAttribute('type', 'number');
        copiesInput.setAttribute('name', 'copies');
        copiesInput.setAttribute('id', 'book-release-form-copies');
        copiesInput.setAttribute('class', 'form-control');
        copiesGroup.appendChild(copiesLabel);
        copiesGroup.appendChild(copiesInput);

        const receiversGroup = document.createElement('div');
        receiversGroup.setAttribute('class', 'form-group');
        const receiverLabel = document.createElement('label');
        receiverLabel.setAttribute('for', 'book-release-form-receiver');
        receiverLabel.textContent = 'Pobierający książki';
        const receiverSelect = document.createElement('select');
        receiverSelect.setAttribute('name', 'receivers');
        receiverSelect.setAttribute('id', 'book-release-form-receiver');
        receiverSelect.setAttribute('class', 'form-control');
        for (const receiver of receivers) {
            const receiverOption = document.createElement('option');
            receiverOption.setAttribute('value', receiver.id);
            receiverOption.textContent = receiver.name;
            receiverSelect.appendChild(receiverOption);
        }
        receiversGroup.appendChild(receiverLabel);
        receiversGroup.appendChild(receiverSelect);
        
        const commentGroup = document.createElement('div');
        commentGroup.setAttribute('class', 'form-group');
        const commentLabel = document.createElement('label');
        commentLabel.setAttribute('for', 'book-release-form-comment');
        commentLabel.textContent = 'Komentarz';
        const text = document.createElement('textarea');
        text.setAttribute('name', 'comment');
        text.setAttribute('id', 'book-release-form-comment');
        text.setAttribute('class', 'form-control');
        commentGroup.appendChild(commentLabel);
        commentGroup.appendChild(text);

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

        form.appendChild(copiesGroup);
        form.appendChild(receiversGroup);
        form.appendChild(commentGroup);
        form.appendChild(buttonsGroup);

        return form;
    };

    return {
        tableBodyContent: tableBodyContent,
        simpleBookActionDiv: simpleBookActionDiv,
        simpleBookActionForm: simpleBookActionForm,
        deleteDiv: deleteDiv,
        editForm: editForm,
        releaseDiv: releaseDiv,
        releaseForm: releaseForm
    };
}();
