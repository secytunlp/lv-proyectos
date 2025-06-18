function toggleDivOnSelect(selectId, targetDivId, matchText) {
    var selectedText = $("#" + selectId + " option:selected").text().trim();

    if (selectedText.includes(matchText)) {
        $("#" + targetDivId).show();
    } else {
        $("#" + targetDivId).hide();
    }
}

$(document).ready(function() {
    // Puedes enlazar tantos selects como quieras aquí
    $("#convocatoria_id").change(function() {
        toggleDivOnSelect('convocatoria_id', 'divMecanismo', 'Equivalencia');
    });

    // También correrlo al cargar la página
    toggleDivOnSelect('convocatoria_id', 'divMecanismo', 'Equivalencia');
});
