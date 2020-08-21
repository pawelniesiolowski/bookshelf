const Receiver = function () {
    const create = function (form) {
        const receiver = {
            name: form.namedItem('name').value,
            surname: form.namedItem('surname').value,
        };
        return receiver;
    };

    return {
        create: create
    };
}();
