const ReceiversElements = function () {
    const tableBodyContent = function (receivers) {
        const content = document.createDocumentFragment();
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
            tr.appendChild(td2);

            const td3 = document.createElement('td');
            td3.setAttribute('data-id', receiver.id);
            const editReceiverButton = document.createElement('button');
            editReceiverButton.setAttribute('class', 'edit-receiver-button action-button btn btn-secondary');
            editReceiverButton.textContent = 'Edytuj';
            const deleteReceiverButton = document.createElement('button');
            deleteReceiverButton.setAttribute('class', 'delete-receiver-button action-button btn btn-danger');
            deleteReceiverButton.textContent = 'Usuń';
            td3.appendChild(editReceiverButton);
            td3.appendChild(deleteReceiverButton);
            tr.appendChild(td3);
            content.appendChild(tr);
        }
        return content;
    };

    return {
        tableBodyContent: tableBodyContent
    };
}();
