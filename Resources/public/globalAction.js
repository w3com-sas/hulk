import {apiJsonRequest} from "./utils";

export const renderGlobalActions = (globalActions) => {
    globalActions.map((globalAction) => {
        let callFunction = new CallFunction(globalAction.type + globalAction.index, globalAction.icon),
            lines = $('table').DataTable().rows('.selected').data().toArray();
        switch (globalAction.type) {
            case "render-form":
                renderForm(globalAction);
                break;
            case "api-call":
                renderForm(globalAction);
                break;
        }
    })
};

export const initUpdateSap = (globalAction) => {
    var url = hulkUrls.updateSap;
    var postData = {
        "lines": lines,
        "entityName": targetEntity,
        "displayEntityKey": targetField,
        "entityKey": entityKey,
        "targetData": targetData
    };


    $.ajax({
        method: 'POST',
        url: url,
        data: postData,
        dataType: 'json',
        success: function () {

            var tpl = '<p class="text-success text-center">' +
                '<i class="fas fa-thumbs-up mr-2"></i>' +
                'Mis à jour avec succès</p>';

            if (modal.find('.modal-body').has('p').length >= 1) {
                modal.find('.modal-body').find('p').remove();
            }
            modal.find('.modal-body').append(tpl);
            setTimeout(window.location.reload(), 1500);

        },
        error: function (xhr) {

            var message = 'Une erreur inconnue est survenue, contactez le support';
            var tpl = '' +
                '<p class="text-danger text-center">' +
                '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                message + '</p>';

            if (modal.find('.modal-body').has('p').length >= 1) {
                modal.find('.modal-body').find('p').remove();
            }
            modal.find('.modal-body').append(tpl);
            modal.find('.btn-success').html('<i class="far fa-paper-plane mr-2"></i>Valider').removeAttr('disabled');
        }
    })
};

// TODO : code mort ?
export const renderForm = (globalAction, lines) => {
    document.addEventListener(globalAction.renderElements.btn.id, () => {

    });
    apiJsonRequest(globalAction.url, data)
        .then((resp) => {
            if (typeof resp.template !== "undefined") {
                callFunction.modalBody.innerHTML = resp.template;
                let form = callFunction.modalBody.querySelector('form');

                if (typeof form !== "undefined") {

                }
                callFunction.addValidateBtn(() => {
                    let data = typeof form !== "undefined" ? {
                        lines: lines,
                        form: $(form).serializeArray()
                    } : {lines: lines};
                    sendRequest(globalAction.url, data);
                });
            }

            if (typeof resp.reload !== "undefined" && resp.reload === true) {
                window.location.reload();
            }
        });
};