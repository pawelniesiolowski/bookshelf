const Book = function () {

    const create = function (form) {
        const book = {
            authors: [],
            title: form.namedItem('title').value,
            ISBN: form.namedItem('ISBN').value,
            price: form.namedItem('price').value,
        };

        if (copies = form.namedItem('copies')) {
            book.copies = copies.value;
        }

        for (let i = 0; i < 10; i++) {
            const nameInput = form.namedItem('authorName[' + i + ']');
            const surnameInput = form.namedItem('authorSurname[' + i + ']');

            if (nameInput === null || surnameInput === null) {
                break;
            }

            const author = {
                name: nameInput.value,
                surname: surnameInput.value,
            };

            if (author.name !== '' || author.surname !== '') {
                book.authors.push(author);
            }
        }

        return book;
    };

    return {
        create: create
    };
}();
