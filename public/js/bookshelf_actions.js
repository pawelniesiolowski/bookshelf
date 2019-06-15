const BookshelfActions = function () {

    const loadBooks = function () {
        const request = new XMLHttpRequest();
        request.open('GET', BookshelfData.table.getAttribute('data-index'), true);
        request.onload = function () {
            let response = {};
            if (request.status === 200) {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('books')) {
                    BookshelfElements.tableBodyContent(response.books, BookshelfData.table);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się pobrać danych');
        };
        request.send();
    };


    return {
        loadBooks: loadBooks
    };
}();
