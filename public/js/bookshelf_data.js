const BookshelfData = function () {
    const table = document.getElementById('bookshelf-table-body');

    const paths = {
        receiveBooks: table.getAttribute('data-receive-path'),
        releaseBooks: table.getAttribute('data-release-path'),
        sellBooks: table.getAttribute('data-sell-path'),
        editBooks: table.getAttribute('data-edit-book-path'),
        getBooks: table.getAttribute('data-get-book-path'),
        deleteBooks: table.getAttribute('data-delete-book-path'),
        getReceivers: table.getAttribute('data-get-receivers-path')
    };

    const createPath = function (path, bookId) {
        return path.replace('0', bookId);
    };

    return {
        table: table,
        paths: paths,
        createPath: createPath
    };
}();
