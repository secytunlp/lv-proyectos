$(document).ready(function () {
    window.cambiosRealizados = false; // expose to global scope

    $('input, select, textarea').on('change', function() {
        window.cambiosRealizados = true;
    });

    window.addEventListener('beforeunload', function (event) {
        if (window.cambiosRealizados) {
            var mensaje = "¡Atención! Puede perder algunos cambios. ¿Estás seguro de abandonar la página?";
            event.returnValue = mensaje;
            return mensaje;
        }
    });

    $('form').on('submit', function() {
        window.cambiosRealizados = false;
    });
});
