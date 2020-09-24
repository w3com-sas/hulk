document.addEventListener('DOMContentLoaded', () => {
    initDisplayChoicesType();
}, false)

const initDisplayChoicesType = () => {
    let displayChoicesColumns = document.querySelectorAll('.display-choices-type');

    for (let i = 0; i < displayChoicesColumns.length; i++) {
        let choices = displayChoicesColumns[i].querySelectorAll('.dropdown-item');

        for (let x = 0; x < choices.length; x++) {
            choices[x].addEventListener('click', (e) => {

                let displayName = e.target.innerText,
                    dropDown = e.target.parentElement.parentElement,
                    btnDropDown = dropDown.querySelector('.dropdown-toggle');

                displayChoicesColumns[i].dataset.display = displayName;
                btnDropDown.innerText = displayName;
                $('table').DataTable().rows().invalidate().draw();
            }, false);
        }
    }
}