const Receivers = function () {
    const init = function () {
        const newReceiver = document.getElementById('bookshelf-new-receiver');
        newReceiver.addEventListener('submit', function (e) {
            e.preventDefault();
            const receiver = create(e.target.elements);
            const path = e.target.getAttribute('action');
            makePostRequest(receiver, path);
        });
        table.addEventListener('click', function (e) { e.preventDefault(); checkEvent(e); });
        loadReceivers();
    };

    const table = document.getElementById('bookshelf-receivers-table-body');

    const receiversPath = table.getAttribute('data-path');

    const deleteReceiverPath = table.getAttribute('data-delete-receiver-path');

    const create = function (form) {
        const receiver = {
            name: form.namedItem('name').value,
            surname: form.namedItem('surname').value,
        };
        return receiver;
    };

    const makePostRequest = function (receiver, path) {
        const request = new XMLHttpRequest();
        request.open('POST', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 201) {
                loadReceivers();
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
        request.send(JSON.stringify(receiver));
    };

    const loadReceivers = function () {
        const request = new XMLHttpRequest();
        request.open('GET', receiversPath, true);
        request.onload = function () {
            let response = {};
            if (request.status === 200) {
                response = JSON.parse(this.response);
                if (response.hasOwnProperty('receivers')) {
                    showReceivers(response.receivers);
                }
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się pobrać danych');
        };
        request.send();
    };

    const showReceivers = function (receivers) {
        while (table.firstChild) {
            table.removeChild(table.firstChild);
        }
        for (let receiver of receivers) {
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', receiver.id);
            const td1 = document.createElement('td');
            td1.textContent = receiver.name;
            tr.appendChild(td1);
            const td2 = document.createElement('td');
            
            for (let receiverEvent of receiver.events) {
                const eventTr = document.createElement('tr');
                const eventTd = document.createElement('td');
                eventTd.textContent = receiverEvent;
                eventTr.appendChild(eventTd);
                td2.appendChild(eventTr);
            }
            tr.appendChild(td2);

            const td3 = document.createElement('td');
            const editReceiverButton = document.createElement('button');
            editReceiverButton.setAttribute('class', 'edit-receiver-button action-button btn btn-secondary');
            editReceiverButton.textContent = 'Edytuj';
            const deleteReceiverButton = document.createElement('button');
            deleteReceiverButton.setAttribute('class', 'delete-receiver-button action-button btn btn-danger');
            deleteReceiverButton.textContent = 'Usuń';
            td3.appendChild(editReceiverButton);
            td3.appendChild(deleteReceiverButton);
            tr.appendChild(td3);
            
            table.appendChild(tr);
        }
    };

    const checkEvent = function (e) {
        const receiverId = e.target.parentElement.getAttribute('data-id');
        if (e.target.classList.contains('delete-receiver-button')) {
            console.log('Tak jest');
            deleteReceiver(deleteReceiverPath.replace('0', receiverId));
        }
    };

    const deleteReceiver = function (path) {
        const request = new XMLHttpRequest();
        request.open('DELETE', path, true);
        request.onload = function () {
            let response = {};
            if (request.status === 200) {
                loadReceivers();
            }
        };
        request.onerror = function () {
            console.log('Błąd! Nie udało się usunąć książki');
        };
        request.send();
    };

    return {
        init: init
    };
}();

Receivers.init();
