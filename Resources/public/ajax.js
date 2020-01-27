export const updateSapLine = (input) => {
    if (input.className.indexOf('is-valid') !== -1) {
        input.className = input.className.replace('is-valid', '');
    }
    if (input.className.indexOf('is-invalid') !== -1) {
        input.className = input.className.replace('is-invalid', '');
    }
    var data = {
        'entity': input.dataset.entity,
        'key': input.dataset.key,
        'targetField': input.dataset.fieldName,
        'targetData': input.value
    };

    $.ajax({
        method: 'POST',
        url: hulkUrls.updateSapLine,
        data: data,
        dataType: 'json',
        success: function (resp) {
            input.className += ' is-valid';
        },
        fail: function () {
            input.className += ' is-invalid';
        }
    });
};

export const updateSap = (data, targetEntity, targetField, entityKey, targetData, modal) => {
    var rows = this.formatDataToUpdate(data);
    var url = hulkUrls.updateSap;
    var postData = {
        "data": rows,
        "targetEntity": targetEntity,
        "targetField": targetField,
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

export const apiRequest = function (data, params, urlApi, modal) {
    var rows = this.formatDataToUpdate(data);
    var postData = {
        "data": rows,
        "apiParams": params,
        "urlApi": urlApi
    };

    $.ajax({
        method: 'POST',
        url: hulkUrls.apiRequest,
        data: postData,
        dataType: 'json',
        success: function (response) {

            var reportTpl = document.importNode(document.getElementById('reporting').content, true);
            var modalSuccess = reportTpl.querySelectorAll('.card-body')[0];
            var modalError = reportTpl.querySelectorAll('.card-body')[1];

            if (response.errors !== undefined) {

                reportTpl.querySelector('.text-danger').textContent = 'Nombre d\'erreur : ' + response.errors.length;

                for (var i = 0; i < response.errors.length; i++) {
                    var tpl = document.createElement('p');
                    tpl.textContent = response.errors[i];
                    modalError.innerHTML += tpl.outerHTML;
                }
            }

            if (response.success !== undefined) {

                reportTpl.querySelector('.text-success').textContent = 'Nombre de réussite : ' + response.success.length;

                for (var i = 0; i < response.success.length; i++) {
                    var tpl = document.createElement('p');
                    tpl.textContent = response.success[i];
                    modalSuccess.innerHTML += tpl.outerHTML;
                }
            }

            if (modal.find('.modal-body').has('p').length >= 1) {
                modal.find('.modal-body').find('p').remove();
            }

            modal.find('.modal-body').append(reportTpl);
            var btn = document.createElement('button');
            btn.innerText = 'Actualiser';
            btn.className = 'btn btn-success btn-block';
            btn.onclick = () => window.location.reload();
            modal.find('.modal-footer').html(btn);
            modal.on('hidden.bs.modal', function () {
                window.location.reload();
            })

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

export const reloadDisplayForm = () => {

    var event = new CustomEvent('LoadDisplayForm');
    document.dispatchEvent(event);

    var data = {};
    data.calcView = document.getElementById('display_calcView').value;
    data.selectedChoices = {};
    data.allFields = {};

    var elements = document.getElementsByTagName('select');
    for (var i = 0; i < elements.length; i++) {
        if (elements[i].value !== "") {
            // Get the real SAP field name
            data.selectedChoices[elements[i].id.replace('display_', '')] = elements[i].value;
        }
        data.allFields[elements[i].id.replace('display_', '')] = elements[i].value;
    }

    $.ajax({
        url: hulkUrls.displayFormReload,
        type: "POST",
        data: data,
        success: function (resp) {
            var fieldsNotToUpdate = Object.keys(data.selectedChoices);
            var toUpdateData = {};
            // Organize data
            var properties = Object.keys(resp);
            for (var l = 0; l < properties.length; l++) {
                if (!fieldsNotToUpdate.includes(properties[l])) {
                    if (!toUpdateData.hasOwnProperty(properties[l])) {
                        toUpdateData[properties[l]] = [];
                    }
                    if (!toUpdateData[properties[l]].includes(resp[properties[l]]) && resp[properties[l]] !== null) {
                        toUpdateData[properties[l]] = Object.keys(resp[properties[l]]).map(function(e) {
                            return resp[properties[l]][e]
                        });
                    }
                }
            }

            // Update option
            var toUpdateFields = Object.keys(toUpdateData);
            for (var i = 0; i < elements.length; i++) {
                for (var l = 0; l < toUpdateFields.length; l++) {
                    if (elements[i].id.replace('display_', '') === toUpdateFields[l]) {
                        $(elements[i]).empty();
                        var nullOption = document.createElement('option');
                        nullOption.value = '';
                        nullOption.text = '';
                        elements[i].add(nullOption);
                        for (var x = 0; x < toUpdateData[toUpdateFields[l]].length; x++) {
                            if (toUpdateData[toUpdateFields[l]][x] !== null){
                                var option = document.createElement('option');
                                option.value = toUpdateData[toUpdateFields[l]][x];
                                option.text = toUpdateData[toUpdateFields[l]][x];
                                elements[i].add(option);
                            }
                        }
                    }
                }
            }
        },
        error: function () {
            alert('Une erreur inconnue est survenue, merci de contacter le support.')
        },
        complete: function () {
            var loadedDisplayFormEvent = new CustomEvent('DisplayFormLoaded');
            document.dispatchEvent(loadedDisplayFormEvent);
        }
    })
};

