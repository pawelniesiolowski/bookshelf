const ErrorsHandler = (function () {

    const show = function (form, errors) {
        for (let [key, value] of Object.entries(JSON.parse(errors).errors)) {
            const errorDiv = form.getElementsByClassName(`error-${key}`).item(0);
            if (errorDiv !== null) {
                errorDiv.textContent = value;
            }
        }
    };

    return {
        show
    };
})();
