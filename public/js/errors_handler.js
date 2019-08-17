const ErrorsHandler = (function () {

    const show = function (form, errors) {
        for (let [key, values] of Object.entries(errors)) {
            let className = key;
            let text = values;
            if (typeof values === 'object') {
                for (let num of Object.keys(values)) {
                    for (let [key, value] of Object.entries(values[num])) {
                        className = key + '[' + num + ']';
                        text = value;
                        const errorDiv = form.getElementsByClassName(`error-${className}`).item(0);
                        if (errorDiv !== null) {
                            errorDiv.textContent = text;
                        }
                    }
                }
                continue;
            }
            const errorDiv = form.getElementsByClassName(`error-${className}`).item(0);
            if (errorDiv !== null) {
                errorDiv.textContent = text;
            }

        }
    };

    const reset = function (form) {
        const errors = form.getElementsByClassName('form-error');
        for (let errorDiv of errors) {
            errorDiv.textContent = '';
        }
    };

    return {
        show,
        reset
    };
})();
