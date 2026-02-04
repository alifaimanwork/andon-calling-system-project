export var BootstrapModalDialog = {
    show: function (modalId, resultCallback) {
        let modalDom = document.getElementById(modalId);

        let result = undefined;

        let dialogResultButtons = $(modalDom).find('.dialog-result');

        let clickCallback = function (e) {
            result = $(e.target).data('dialog-result');
        }

        let closeCallback = function (e) {
            //cleanup
            removeEventListeners();

            //callback
            resultCallback(result);
        }

        let removeEventListeners = function () {
            modalDom.removeEventListener('hidden.bs.modal', closeCallback);
            dialogResultButtons.off('click', clickCallback);
        }

        dialogResultButtons.on('click', clickCallback);

        let bsModal = new bootstrap.Modal(modalDom, {
            keyboard: false
        });

        modalDom.addEventListener('hidden.bs.modal', closeCallback);
        bsModal.show();
    }
};