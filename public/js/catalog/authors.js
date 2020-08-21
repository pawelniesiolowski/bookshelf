const Authors = (function () {
    let authorNumber = 1;

    const createDisplayedAuthors = function (authors) {
        let displayedAuthors = '';
        let amountOfAuthors = Object.keys(authors).length;
        if (amountOfAuthors > 0) {
            for (author of authors) {
                amountOfAuthors--;
                displayedAuthors += author.surname + ' ' + author.name;
                if (amountOfAuthors > 0) {
                    displayedAuthors += ', ';
                }
            }
        }
        return displayedAuthors;
    };

    const addAuthor = function () {
        const authorGroup = document.getElementById('bookshelf-new-book-authorFormGroup');

        const newAuthorRow = document.createElement('div');
        newAuthorRow.setAttribute('class', 'form-row');

        const authorSurnameDiv = document.createElement('div');
        authorSurnameDiv.setAttribute('class', 'col-md-6');
        const authorSurnameInput = document.createElement('input');
        authorSurnameInput.setAttribute('type', 'text');
        authorSurnameInput.setAttribute('name', 'authorSurname[' + authorNumber + ']');
        authorSurnameInput.setAttribute('id', 'bookshelf-new-book-author-surname[' + authorNumber + ']');
        authorSurnameInput.setAttribute('class', 'form-control');
        authorSurnameDiv.appendChild(authorSurnameInput);
        const authorSurnameError = document.createElement('div');
        authorSurnameError.setAttribute('class', 'form-error error-authorSurname[' + authorNumber + ']');
        authorSurnameDiv.appendChild(authorSurnameError);
        newAuthorRow.appendChild(authorSurnameDiv);


        const authorNameDiv = document.createElement('div');
        authorNameDiv.setAttribute('class', 'col-md-6');
        const authorNameInput = document.createElement('input');
        authorNameInput.setAttribute('type', 'text');
        authorNameInput.setAttribute('name', 'authorName[' + authorNumber + ']');
        authorNameInput.setAttribute('id', 'bookshelf-new-book-author-name[' + authorNumber + ']');
        authorNameInput.setAttribute('class', 'form-control');
        authorNameDiv.appendChild(authorNameInput);
        const authorNameError = document.createElement('div');
        authorNameError.setAttribute('class', 'form-error error-authorName[' + authorNumber + ']');
        authorNameDiv.appendChild(authorNameError);
        newAuthorRow.appendChild(authorNameDiv);

        authorGroup.appendChild(newAuthorRow);
        authorNumber++;
    };

    return {
        createDisplayedAuthors: createDisplayedAuthors,
        addAuthor: addAuthor
    }
})();
