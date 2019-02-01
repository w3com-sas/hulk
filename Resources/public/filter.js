$('.custom-select').on('change', function () {
    console.log(this.value);
    table
        .column($(this).attr('colIndex'))
        .search(this.value)
        .draw()
});
$('#searchBar').on('keyup change', function () {
    table
        .search(this.value)
        .draw()
});

$('.multiple-filter').on('keyup change', function () {
    table.draw();
});