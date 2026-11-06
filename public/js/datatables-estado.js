/*
 * Estado guardado de las tablas (stateSave) — comportamiento común a todo el sistema.
 *
 * 1. El estado se guarda por URL completa (ruta + query string). El listado general de un módulo y
 *    la vista filtrada por un padre (p. ej. joven_evaluacions?joven_id=123) recuerdan sus filtros por
 *    separado: entrar desde el ícono de una fila ya no hereda la página ni la búsqueda del listado
 *    general. Para los listados sin query string la clave es la misma que usaba DataTables por
 *    defecto, así que lo ya guardado se conserva.
 * 2. Si el estado restaurado apunta a una página que ya no existe (quedó en la 5 y ahora hay menos
 *    registros), la tabla vuelve sola a la primera en lugar de mostrarse vacía.
 *
 * Incluir después de jquery.dataTables.min.js y antes del script que inicializa la tabla.
 * Requiere DataTables >= 1.10.13 (stateLoadCallback sincrónico). Hoy: 1.10.16.
 */
(function ($) {
    if (!$ || !$.fn || !$.fn.dataTable) {
        return;
    }

    function clave(settings) {
        return 'DataTables_' + settings.sInstance + '_' + location.pathname + location.search;
    }

    $.extend(true, $.fn.dataTable.defaults, {
        stateSaveCallback: function (settings, data) {
            try {
                localStorage.setItem(clave(settings), JSON.stringify(data));
            } catch (e) {}
        },
        stateLoadCallback: function (settings) {
            try {
                return JSON.parse(localStorage.getItem(clave(settings)));
            } catch (e) {
                return null;
            }
        }
    });

    $(document).on('draw.dt', function (e, settings) {
        var api = new $.fn.dataTable.Api(settings);
        var info = api.page.info();
        if (info.recordsDisplay > 0 && info.start >= info.recordsDisplay) {
            // Diferido: dentro del propio evento draw DataTables está procesando la respuesta y
            // un draw() inmediato no vuelve a pedir datos al servidor.
            setTimeout(function () {
                api.page('first').draw('page');
            }, 0);
        }
    });
})(window.jQuery);
