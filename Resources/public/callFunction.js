global.CallFunction = class {
    constructor(functionName, headerIconClass) {
        this.modal = document.getElementById(functionName);
        this.modalBody = document.getElementById(functionName + 'Body');
        this.modalHeader = document.getElementById(functionName + 'Header');
        this.modalFooter = document.getElementById(functionName + 'Footer');
        this.modalTextLoading = document.getElementById(functionName + 'TextLoading');
        this.toSendRows = [];
        this.modalBody.innerHTML = '';
        this.modalFooter.innerHTML = '';

        var headerIcon = document.createElement('i');
        headerIcon.className = headerIconClass;
        this.headerIcon = headerIcon;

        var containerSuccess = document.createElement('div');
        containerSuccess.className = 'card border-success p-2 mt-2';
        this.containerSuccess = containerSuccess;

        var containerErrors = document.createElement('div');
        containerErrors.className = 'card border-danger p-2';
        this.containerErrors = containerErrors;
    };

    showModal(headerText) {
        $(this.modalTextLoading).html(headerText);
        $(this.modalHeader).find('.icon-item').html(this.headerIcon.outerHTML);
        $(this.modalBody).removeClass('d-none');
        $(this.modalHeader).removeClass('d-none');
        $(this.modalHeader).find('.icon-item').removeClass('d-none');
        $(this.modal).modal('show');
    };

    startLoading() {
        this.modalFooter.className = 'loading-modal';
        var loadingIcon = document.createElement('i');
        loadingIcon.className = 'fad fa-circle-notch fa-3x text-success fa-spin';
        $(this.modalHeader).find('.icon-loading').html(loadingIcon.outerHTML);
        $(this.modalHeader).find('.icon-loading').removeClass('d-none');
        $(this.modalHeader).find('.icon-item').addClass('d-none');
        $(this.modalFooter).find('button').addClass('d-none');
    };

    stopLoading() {
        $(this.modalFooter).removeClass('loading-modal');
        $(this.modalFooter).addClass('modal-footer');
        $(this.modalHeader).find('.icon-loading').addClass('d-none');
        $(this.modalHeader).find('.icon-item').removeClass('d-none');
        $(this.modalFooter).find('button').removeClass('d-none');

    };

    addValidateBtn(functionToCall) {
        var btnCancel = document.createElement('button');
        btnCancel.className = 'btn btn-danger mr-auto';
        var iconCancel = document.createElement('i');
        iconCancel.className = 'fad fa-times mr-2';
        btnCancel.innerHTML = iconCancel.outerHTML + 'Annuler';
        btnCancel.dataset.dismiss = 'modal';

        var btnValidate = document.createElement('button');
        btnValidate.className = 'btn btn-success ml-auto';
        var iconValidate = document.createElement('i');
        iconValidate.className = 'fad fa-check mr-2';
        btnValidate.innerHTML = iconValidate.outerHTML + 'Valider';
        btnValidate.onclick = () => functionToCall();
        this.modalFooter.appendChild(btnCancel);
        this.modalFooter.appendChild(btnValidate);
        $(this.modalFooter).removeClass('d-none');
    };

    showLinesSelected(rowsLength) {
        var alertInfo = document.createElement('div');
        alertInfo.className = 'text-gray my-3 text-center';
        var iconInfo = document.createElement('i');
        iconInfo.className = 'fad fa-info-circle mr-2';
        alertInfo.innerHTML = iconInfo.outerHTML + 'Vous avez sélectionné ' + rowsLength + ' ligne' + (rowsLength === 1 ? '' : 's') + ', voulez-vous continuer ?';
        $(this.modalBody).html(alertInfo);
    }

    addLineResult(message, type) {
        var icon = document.createElement('i');
        icon.className = type === 'success' ? 'fad fa-check mr-2' : 'fad fa-times mr-2';
        var p = document.createElement('p');
        p.className = 'text-'+type;
        p.appendChild(icon);
        p.innerText = message;
        this.modalBody.appendChild(p);
    }

    addReloadButton() {
        var reloadButton = document.createElement('button');
        reloadButton.className = 'btn btn-success btn-block';
        reloadButton.innerText = 'Actualiser';
        reloadButton.onclick = () => window.location.reload();
        this.modalFooter.innerHTML = '';
        this.modalFooter.appendChild(reloadButton);
        $(this.modal).on('hide.bs.modal', function () {
            window.location.reload();
        })
    }

    addUnknowError() {
        this.modalBody.innerHTML = '';
        var errors = document.createElement('div');
        errors.className = 'alert alert-danger';
        errors.innerText = 'Une erreur inconnue est survenue.';
        this.modalBody.appendChild(errors);
    }
};

global.checkRowsLength = function(rows, minLength, callFunction) {
    if (rows.length < minLength) {
        var errors = document.createElement('div');
        errors.className = 'alert alert-danger';
        errors.innerText = 'Vous devez sélectionner au moins ' + minLength + ' ligne' + (minLength > 1 ? 's.' : '.');
        callFunction.modalBody.appendChild(errors);
        return false;
    }
    return true;
};
