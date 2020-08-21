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

export const temporizeUpdate = (id,visible) => {
    let toTemporize = document.getElementById(id);
    let container = toTemporize.parentNode;
    let id_temporize = id + '_temporize';
    if(document.getElementById(id_temporize) == null){
        let element = document.createElement('div');
        let icon = document.createElement('i');
        icon.classList.add('fad');
        icon.classList.add('fa-spinner-third');
        icon.classList.add('fa-spin');
        icon.classList.add('float-left');

        element.classList.add('p-2');
        element.setAttribute('id',id_temporize);
        element.classList.add('w-100');
        element.classList.add('text-center');
        element.classList.add('bg-gray-1');
        element.append(icon);
        element.append('Actualisation des filtres ...');

        container.append(element);
    }
    let temporizater = document.getElementById(id_temporize);

    if(visible){
        if(!toTemporize.classList.contains('d-none')){
            toTemporize.classList.add('d-none');
        }
        if(temporizater.classList.contains('d-none')){
            temporizater.classList.remove('d-none');
        }
    } else {
        if(toTemporize.classList.contains('d-none')){
            toTemporize.classList.remove('d-none');
        }
        if(!temporizater.classList.contains('d-none')){
            temporizater.classList.add('d-none');
        }
    }
}
