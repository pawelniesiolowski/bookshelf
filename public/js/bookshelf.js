const Bookshelf = function () {
    
    const init = function () {
        BookshelfData.table.addEventListener('click', function (e) { checkEvent(e); });
        const newBook = document.getElementById('bookshelf-new-book');
        newBook.addEventListener('submit', function (e) { Book.create(e, BookshelfActions.loadBooks); });
        BookshelfActions.loadBooks();
    };

    const checkEvent = function (e) {
        const bookId = e.target.parentElement.getAttribute('bookId');
        const getBookPath = BookshelfData.createPath(BookshelfData.paths.getBooks, bookId);
        if (e.target.classList.contains('receive-button')) {
            const receiveBookPath = BookshelfData.createPath(BookshelfData.paths.receiveBooks, bookId);
            Book.receive(receiveBookPath, getBookPath);
        } else if (e.target.classList.contains('release-button')) {
            const releaseBooksPath = BookshelfData.createPath(BookshelfData.paths.releaseBooks, bookId);
            const getReceiversPath = BookshelfData.paths.getReceivers;
            Book.release(releaseBooksPath, getBookPath, getReceiversPath);
        } else if (e.target.classList.contains('sell-button')) {
            const path = BookshelfData.createPath(BookshelfData.paths.sellBooks, bookId);
            const sellDiv = createSellDiv(path);
            // ModalWindow.init(sellDiv);
        } else if (e.target.classList.contains('edit-book-button')) {
            const editBooksPath = BookshelfData.createPath(BookshelfData.paths.editBooks, bookId);
            Book.edit(editBooksPath, getBookPath);
        } else if (e.target.classList.contains('delete-book-button')) {
            const deleteBooksPath = BookshelfData.createPath(BookshelfData.paths.deleteBooks, bookId);
            Book.deleteBook(deleteBooksPath, getBookPath);
        }
    };

    return {
        init: init
    };
}();

Bookshelf.init();
