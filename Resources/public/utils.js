export const countSelectedRows = (rows) => {
    var node = document.getElementById('countSelectedRows');
    node.innerText = rows.length + ' ligne(s) sélectionnée(s)';
    node.style.display = 'block';
};

export const openForm = (obj, id) => {
    sap.sbo.webbridge.openForm(obj, id)
};

export const goToLine = (lineIndex, idLine) => {
    var table = $('#dataTable').DataTable();
    var pageToGo = Math.floor(lineIndex / table.page.len());
    table.page(pageToGo).draw('page');
    document.getElementById(idLine).scrollIntoView();
};



