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

        const author = {
            name: form.namedItem('authorName').value,
            surname: form.namedItem('authorSurname').value,
        };
        
        if (author.name !== '' || author.surname !== '') {
            book.authors.push(author);
        }

        return book;
    };

    return {
        create: create
    };
}();
