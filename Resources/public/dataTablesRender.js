window.renderCheckbox = function(data) {
    return $('#checkboxTpl').html().replace('idLine', data);
};

window.renderUpdateSap = function(fieldName, entity, entityKey) {
    return $('updateSap'+fieldName)
        .replace('[[entity]]', entity)
        .replace('[[entityKey]]', entityKey)
        .replace('[[fieldName]]', fieldName);
};
window.renderUpdateSapData = function(data) {
    return $('updateSap'+fieldName)
        .replace('[[data]]', fieldName);
};

window.renderIcon = function(data, type, row) {
    var tpl = $('#icon').html();
    if (data.split(' ').length == 1) {
        data = 'fa fa-' + data;
    }
    return tpl.replace('[[data]]', data);
};