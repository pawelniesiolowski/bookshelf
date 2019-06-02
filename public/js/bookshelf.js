const Bookshelf = function () {
    const bookshelfTableBody = document.getElementById('bookshelf-table-body');
    
    const init = function () {
        bookshelfTableBody.addEventListener('click', function (e) { checkEvent(e); });
        const newBook = document.getElementById('bookshelf-new-book');
        newBook.addEventListener('submit', function (e) { addBook(e); });
        loadBooks();
    };

    const loadBooks = function () {
        const request = new XMLHttpRequest();
        request.open('GET', bookshelfTableBody.getAttribute('data-index'), true);
        request.onload = function () {
            let response = {};
            if (request.status === 200) {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('books')) {
                    createBookshelfTableBodyContent(response.books, bookshelfTableBody);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się pobrać danych');
        };
        request.send();
    };

    const checkEvent = function (e) {
        if (e.target.classList.contains('receive-button')) {
            const receiveDiv = createReceiveDiv(e.target.getAttribute('data-path'));
            ModalWindow.init(receiveDiv);
        } else if (e.target.classList.contains('release-button')) {
            const releaseDiv = createReleaseDiv(e.target.getAttribute('data-path'));
            // ModalWindow.init(releaseDiv);
        } else if (e.target.classList.contains('sell-button')) {
            const sellDiv = createSellDiv(e.target.getAttribute('data-path'));
            // ModalWindow.init(sellDiv);
        }
    }

    const createReceiveDiv = function (path) {
        const div = document.createElement('div');
        const text = document.createElement('p');
        text.textContent = 'Ile egzemplarzy książki chcesz dodać?';
        div.appendChild(text);
        const form = document.createElement('form');
        const input = document.createElement('input');
        input.setAttribute('type', 'number');
        input.setAttribute('name', 'copies');
        form.appendChild(input);
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
            const data = {
                copies: e.target.elements.namedItem('copies').value,
            };
            emitBookChangeEvent(data, path); 
        });
        div.appendChild(form);
        return div;
    }

    const createReleaseDiv = function (path) {}

    const createSellDiv = function (path) {}

    const emitBookChangeEvent = function (data, path) {
        const request = new XMLHttpRequest();
        request.open('POST', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 204) {
                loadBooks();
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

    const createBookshelfTableBodyContent = function (books, tableBody) {
        const receiveBooksPath = tableBody.getAttribute('data-receive-path');
        const releaseBookPath = tableBody.getAttribute('data-release-path');
        const sellBookPath = tableBody.getAttribute('data-sell-path');

        while (tableBody.firstChild) {
            tableBody.removeChild(tableBody.firstChild);
        }

        for (let book of books) {
            const tr = document.createElement('tr');
            const td1 = document.createElement('td');
            td1.textContent = book.author;
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
            
            const receiveButton = document.createElement('button');
            receiveButton.setAttribute('class', 'receive-button action-button btn btn-success');
            receiveButton.setAttribute('data-path', receiveBooksPath.replace('0', book.id));
            receiveButton.textContent = 'Dodaj';
            
            const releaseButton = document.createElement('button');
            releaseButton.setAttribute('class', 'release-button action-button btn btn-warning');
            releaseButton.setAttribute('data-path', releaseBookPath.replace('0', book.id));
            releaseButton.textContent = 'Wydaj';
            
            const sellButton = document.createElement('button');
            sellButton.setAttribute('class', 'sell-button action-button btn btn-info');
            sellButton.setAttribute('data-path', sellBookPath.replace('0', book.id));
            sellButton.textContent = 'Sprzedaj';
            
            td6.appendChild(receiveButton);
            td6.appendChild(releaseButton);
            td6.appendChild(sellButton);
            tr.appendChild(td6);
            
            tableBody.appendChild(tr);
        }

        return tableBody;
    };

    const addBook = function (e) {
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
        
        const request = new XMLHttpRequest();
        request.open('POST', newBookForm.getAttribute('action'), true);
        request.onload = function () {
            let response = {};
            if (request.status === 201) {
                loadBooks();
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
        init: init
    };
}();

Bookshelf.init();
