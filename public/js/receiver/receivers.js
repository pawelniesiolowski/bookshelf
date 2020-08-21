const Receivers = function () {
    const table = document.getElementById('bookshelf-receivers-table-body');
    const paths = {
        index: table.getAttribute('data-path'),
        deleteReceiver: table.getAttribute('data-delete-receiver-path'),
        get: table.getAttribute('data-get-receiver-path'),
        edit: table.getAttribute('data-edit-receiver-path')
    };

    const init = function () {
        const newReceiver = document.getElementById('bookshelf-new-receiver');
        newReceiver.addEventListener('submit', function (e) {
            e.preventDefault();
            ErrorsHandler.reset(newReceiver);
            const receiver = Receiver.create(e.target.elements);
            const path = e.target.getAttribute('action');
            post(receiver, path)
                .then(function () {
                    loadReceivers(paths.index);
                })
                .catch(function (errors) {
                    ErrorsHandler.show(newReceiver, JSON.parse(errors).errors);
                });
        });
        table.addEventListener('click', function (e) { e.preventDefault(); checkEvent(e); });
        loadReceivers();
    };

    const post = function (receiver, path) {
        return new Promise(function (resolve, reject) {
            const request = new XMLHttpRequest();
            request.open('POST', path, true);
            request.onload = function () {
                if (request.status === 201) {
                    resolve();
                } else {
                    reject(this.response);
                }
            };
            request.onerror = function () {
                reject(Error('Błąd! Nie udało się dodać książki'));
            };
            request.send(JSON.stringify(receiver));
        });
    };

    const loadReceivers = function () {
        fetchReceivers(paths.index)
            .then(decode)
            .then(function (data) {
                const content = ReceiversElements.tableBodyContent(data.receivers);
                showReceivers(content);
            });
    };

    const fetchReceivers = function (path) {
        return fetch(path);
    };

    const decode = function (response) {
        return response.json();
    };

    const showReceivers = function (content) {
        while (table.firstChild) {
            table.removeChild(table.firstChild);
        }
        table.appendChild(content);
    };

    const checkEvent = function (e) {
        const receiverId = e.target.parentElement.getAttribute('data-id');
        if (e.target.classList.contains('delete-receiver-button')) {
            deleteReceiver(receiverId)
                .then(loadReceivers)
                .catch(function(errors) {
                    console.log(errors);
                });
        } else if (e.target.classList.contains('edit-receiver-button')) {
            editReceiver(receiverId)
                .then(loadReceivers)
                .catch(function(errors) {
                    console.log(errors);
                });
        }
    };

    const deleteReceiver = function (id) {
        const getPath = createPath(paths.get, id);
        const deletePath = createPath(paths.deleteReceiver, id);

        return fetchReceivers(getPath)
            .then(decode)
            .then(function (data) {
                const text = 'Czy na pewno chcesz usunąć użytkownika: ' + data.receiver.name + '?';
                const approvalDiv = BookshelfElements.deleteDiv(text, function () {
                    ModalWindow.closeModal(); 
                    doDeleteReceiver(deletePath)
                        .then(loadReceivers)
                        .catch(function (errors) {
                            console.log(errors);
                        });
                    
                });
                ModalWindow.init(approvalDiv);
            });
    };

    const doDeleteReceiver = function (path) {
        return fetch(path, {
            method: 'DELETE'
        });
    };

    const editReceiver = function (id) {
        const getPath = createPath(paths.get, id);
        const editPath = createPath(paths.edit, id);

        return fetchReceivers(getPath)
            .then(decode)
            .then(function (data) {
                const div = document.createElement('div');
                const text = document.createElement('h2');
                text.setAttribute('class', 'text-center');
                text.textContent = 'Edycja danych osoby odbierającej książki';
                div.appendChild(text);
                const form = ReceiversElements.editForm(data.receiver);
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    ErrorsHandler.reset(form);
                    const data = Receiver.create(e.target.elements)
                    doEditReceiver(data, editPath)
                        .then(function () {
                            ModalWindow.closeModal();
                            loadReceivers();
                        })
                        .catch(function (errors) {
                            ErrorsHandler.show(form, JSON.parse(errors).errors);
                        });
                });
                div.appendChild(form);
                ModalWindow.init(div);
            });
    };

    const doEditReceiver = function (data, path) {
        return new Promise(function (resolve, reject) {
            const request = new XMLHttpRequest();
            request.open('PUT', path, true);
            request.onload = function () {
                if (request.status === 204) {
                    resolve();
                } else {
                    reject(this.response);
                }
            };
            request.onerror = function () {
                reject(Error('Błąd! Nie udało się wykonać akcji'));
            };
            request.send(JSON.stringify(data));
        });
    };

    const createPath = function (path, id) {
        return path.replace('0', id);
    };

    return {
        init: init
    };
}();

Receivers.init();
