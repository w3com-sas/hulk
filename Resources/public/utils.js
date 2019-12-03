function countSelectedRows(rows) {
    var node = document.getElementById('countSelectedRows');
    node.innerText = rows.length + ' ligne(s) sélectionnée(s)';
    node.style.display = 'block';
}

function formatDataToUpdate(data) {
    var obj = {};
    $.each(data, function (index, value) {
        obj[String(index)] = value
    });
    return obj;
}

function openForm(obj, id) {
    sap.sbo.webbridge.openForm(obj, id)
}

function goToLine(lineIndex, idLine) {
    var table = $('#dataTable').DataTable();
    var pageToGo = Math.floor(lineIndex / table.page.len());
    table.page(pageToGo).draw('page');
    document.getElementById(idLine).scrollIntoView();
}