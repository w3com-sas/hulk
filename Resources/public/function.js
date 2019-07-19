function openForm(obj, id) {
    sap.sbo.webbridge.openForm(obj, id)
}

function formatDataToCsv(rows) {


    var formatedData = [];

    // On passe le header (Les entêtes)
    formatedData.push(Object.keys(rows[0]));

    // On transform l'objet en tableau pour qu'il puisse être traité.
    $.each(rows, function (indexObj, obj) {
        var line = [];
        $.each(obj, function (index, value) {
            line.push(value)
        });
        formatedData.push(line);
    });
/*
    var formatedData = {};

    // On passe le header (Les entêtes)
    formatedData.header = push(Object.keys(rows[0]));

    // On transform l'objet en tableau pour qu'il puisse être traité.
    $.each(rows, function (indexObj, obj) {
        var line = [];
        $.each(obj, function (index, value) {
            line.push(value)
        });
        formatedData.push(line);
    });*/

    return formatedData;
}

function countSelectedRows(rows) {
    var node = document.getElementById('countSelectedRows');
    node.innerText = rows.length + ' ligne(s) sélectionnée(s)';
    node.style.display = 'block';
}

function disableSubmitButton(obj) {
    $(obj).attr('disabled', true);
    $(obj).html('<i class="fas fa-spinner fa-spin"></i>');
}


function formatDataToUpdate(data) {
    var obj = {};

    $.each(data, function (index, value) {
        obj[String(index)] = value
    });
    return obj;
}

function updateSap(data, targetEntity, targetField, entityKey, targetData, modal) {

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
}

function exportToCsv(filename, rows) {
    var processRow = function (row) {

        var finalVal = '';
        for (var j = 0; j < row.length; j++) {
            var innerValue = row[j] === null ? '' : row[j].toString();

            // Si la donnée est un objet date on la convertie
            if (row[j] instanceof Date) {
                innerValue = row[j].toLocaleString();
            }

            // Traitement des retours ligne
            var result = innerValue.replace(/"/g, '""');
            // Caractère interdit
            if (result.search(/("|;|\n)/g) >= 0)
                result = '"' + result + '"';
            if (j > 0)
                finalVal += ';';
            finalVal += result;
        }
        return finalVal + '\n';
    };

    // Force excel à l'utf-8
    var csvFile = "\uFEFF";
    for (var i = 0; i < rows.length; i++) {
        csvFile += processRow(rows[i]);
    }

    var blob = new Blob([csvFile], {type: 'text/csv;charset=utf-8;'});
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {

        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}

function apiRequest(data, params, urlApi, modal) {

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

            if (response.errors !== undefined){

                reportTpl.querySelector('.text-danger').textContent = 'Nombre d\'erreur : '+response.errors.length;

                for (var i = 0; i < response.errors.length; i++){
                    var tpl = document.createElement('p');
                    tpl.textContent = response.errors[i];
                    modalError.innerHTML += tpl.outerHTML;
                }
            }

            if (response.success !== undefined){

                reportTpl.querySelector('.text-success').textContent = 'Nombre de réussite : '+response.success.length;

                for (var i = 0; i < response.success.length; i++){
                    var tpl = document.createElement('p');
                    tpl.textContent = response.success[i];
                    modalSuccess.innerHTML += tpl.outerHTML;
                }
            }

            if (modal.find('.modal-body').has('p').length >= 1) {
                modal.find('.modal-body').find('p').remove();
            }


            modal.find('.modal-body').append(reportTpl);
            var btn = modal.find('.modal-footer').find('.btn-success')[0];
            btn.disabled = false;
            btn.innerHTML = 'Actualiser';
            btn.onclick = () => window.location.reload();
            //modal.find('.modal-footer').find('.btn-success');

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
}
