global.countSelectedRows = function (rows) {
    var node = document.getElementById('countSelectedRows');
    node.innerText = rows.length + ' ligne(s) sélectionnée(s)';
    node.style.display = 'block';
};

global.formatDataToUpdate = function(data) {
    var obj = {};
    $.each(data, function (index, value) {
        obj[String(index)] = value
    });
    return obj;
};

global.openForm = function (obj, id) {
    sap.sbo.webbridge.openForm(obj, id)
};

global.goToLine = function (lineIndex, idLine) {
    var table = $('#dataTable').DataTable();
    var pageToGo = Math.floor(lineIndex / table.page.len());
    table.page(pageToGo).draw('page');
    document.getElementById(idLine).scrollIntoView();
};


