const ReceiversElements = function () {
    const tableBodyContent = function (receivers) {
        const content = document.createDocumentFragment();
        for (let receiver of receivers) {
            const tr = document.createElement('tr');
            const td1 = document.createElement('td');
            td1.textContent = receiver.name;
            tr.appendChild(td1);
            const td2 = document.createElement('td');
            td2.setAttribute('data-id', receiver.id);
            const editReceiverButton = document.createElement('button');
            editReceiverButton.setAttribute('class', 'edit-receiver-button action-button btn btn-secondary');
            editReceiverButton.textContent = 'Edytuj';
            const deleteReceiverButton = document.createElement('button');
            deleteReceiverButton.setAttribute('class', 'delete-receiver-button action-button btn btn-danger');
            deleteReceiverButton.textContent = 'Usuń';
            td2.appendChild(editReceiverButton);
            td2.appendChild(deleteReceiverButton);
            tr.appendChild(td2);
            content.appendChild(tr);
        }
        return content;
    };

    const editForm = function (receiver) {
        const form = document.createElement('form');

        receiver = receiver.name.split(' ');

        const receiverGroup = document.createElement('div');
        receiverGroup.setAttribute('class', 'form-group');
        const surnameLabel = document.createElement('label');
        surnameLabel.setAttribute('for', 'receiver-edit-form-surname');
        surnameLabel.textContent = 'Nazwisko';
        const surnameInput = document.createElement('input');
        surnameInput.setAttribute('type', 'text');
        surnameInput.setAttribute('name', 'surname');
        surnameInput.setAttribute('id', 'receiver-edit-form-surname');
        surnameInput.setAttribute('class', 'form-control');
        surnameInput.setAttribute('value', receiver[0]);
        const surnameError = document.createElement('div');
        surnameError.setAttribute('class', 'form-error error-surname');
        receiverGroup.appendChild(surnameLabel);
        receiverGroup.appendChild(surnameInput);
        receiverGroup.appendChild(surnameError);

        const nameLabel = document.createElement('label');
        nameLabel.setAttribute('for', 'receiver-edit-form-name');
        nameLabel.textContent = 'Imię';
        const nameInput = document.createElement('input');
        nameInput.setAttribute('type', 'text');
        nameInput.setAttribute('name', 'name');
        nameInput.setAttribute('id', 'receiver-edit-form-name');
        nameInput.setAttribute('class', 'form-control');
        nameInput.setAttribute('value', receiver[1]);
        const nameError = document.createElement('div');
        nameError.setAttribute('class', 'form-error error-name');
        receiverGroup.appendChild(nameLabel);
        receiverGroup.appendChild(nameInput);
        receiverGroup.appendChild(nameError);
        form.appendChild(receiverGroup);

        const buttonsGroup = document.createElement('div');
        buttonsGroup.setAttribute('class', 'form-group text-center');
        const saveButton = document.createElement('button');
        saveButton.setAttribute('class', 'btn btn-success action-button');
        saveButton.setAttribute('type', 'submit');
        saveButton.textContent = 'Zapisz';
        buttonsGroup.appendChild(saveButton);
        const cancelButton = document.createElement('button');
        cancelButton.setAttribute('class', 'btn btn-default action-button');
        cancelButton.textContent = 'Anuluj';
        cancelButton.addEventListener('click', function (e) { e.preventDefault(); ModalWindow.closeModal(); });
        buttonsGroup.appendChild(cancelButton);
        form.appendChild(buttonsGroup);
        return form;
    };

    return {
        tableBodyContent: tableBodyContent,
        editForm: editForm
    };
}();
