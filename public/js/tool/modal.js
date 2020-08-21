const ModalWindow = (function () {

    const init = function (content = '') {
        const previousModalDiv = document.querySelector('.modal-window');
        if (previousModalDiv === null) {
            let modalDiv = createModal();
            appendContentToModal(modalDiv, content);
            bindCenterModal(modalDiv);
            showModal(modalDiv);
            centerModal(modalDiv);
        }
    };

    const createModal = function () {
        const modalDiv = document.createElement('div');
        modalDiv.setAttribute('class', 'modal-window');
        return modalDiv;
    };

    const appendContentToModal = function (modalDiv, content) {
            modalDiv.appendChild(content);
    };

    const bindCenterModal = function (modalDiv) {
        window.addEventListener('resize', function () {
            centerModal(modalDiv);
        });
    };

    const showModal = function (modalDiv) {
        const body = document.getElementsByTagName('body');
        body[0].appendChild(modalDiv);
    };

    const centerModal = function (modalDiv) {
        const height = modalDiv.clientHeight;
        const width = modalDiv.clientWidth;
        const top = (window.innerHeight - height) / 2;
        const left = (window.innerWidth - width) / 2;
        modalDiv.style.cssText = 'top:' + top + 'px;left:' + left + 'px;';
    };

    const closeModal = function () {
        const modalDiv = document.querySelector('.modal-window');
        if (modalDiv) {
            modalDiv.remove();
        }
    };

    return {
        init: init,
        closeModal : closeModal
    };
})();

