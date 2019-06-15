const Receivers = function () {
    const init = function () {
        const newReceiver = document.getElementById('bookshelf-new-receiver');
        newReceiver.addEventListener('submit', function (e) {
            e.preventDefault();
            const receiver = create(e.target.elements);
            const path = e.target.getAttribute('action');
            makePostRequest(receiver, path);
        });
        loadReceivers();
    };

    const table = document.getElementById('bookshelf-receivers-table-body');

    receivers = table.getAttribute('data-path');

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
        request.open('GET', receivers, true);
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
            table.appendChild(tr);
        }
    };

    return {
        init: init
    };
}();

Receivers.init();
