window.countSelectedRows = function(rows) {
    var node = document.getElementById('countSelectedRows');
    node.innerText = rows.length + ' ligne(s) sélectionnée(s)';
    node.style.display = 'block';
};

window.formatDataToUpdate =  function(data) {
    var obj = {};
    $.each(data, function (index, value) {
        obj[String(index)] = value
    });
    return obj;
};

window.openForm = function(obj, id) {
    sap.sbo.webbridge.openForm(obj, id)
};

window.goToLine = function(lineIndex, idLine) {
    var table = $('#dataTable').DataTable();
    var pageToGo = Math.floor(lineIndex / table.page.len());
    table.page(pageToGo).draw('page');
    document.getElementById(idLine).scrollIntoView();
};

window.setDataTablesGlobalVar = function (dataTables) {
    window.table = dataTables;
};


