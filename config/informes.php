<?php

/*
|--------------------------------------------------------------------------
| Informes de proyectos: qué cohortes presentan informe cada año
|--------------------------------------------------------------------------
|
| Clave: año del informe. Dentro, por programa (proyectos.tipo), el par
| "<año inicio>-<año fin>" del proyecto y el tipo de informe que le toca.
| Las cohortes que no están acá no presentan informe ese año.
|
| Lo usan `informes:facultad` y `informes:control-integrantes`.
| Para un año nuevo alcanza con agregar su bloque.
|
*/

return [

    'rangos' => [

        2025 => [
            'I+D' => [
                '2022-2025' => 'Final',            // 4 años, cierra en 2025
                '2023-2026' => 'Reducido 3° año',
                '2024-2025' => 'Final',            // 2 años, cierra en 2025
                '2024-2027' => 'Bienal',
                '2025-2026' => 'Reducido 1° año',
                '2025-2028' => 'Reducido 1° año',
            ],
            'PPID' => [
                '2024-2025' => 'Final',
            ],
        ],

        // 2026 => [
        //     'I+D' => [
        //         '2023-2026' => 'Final',
        //         ...
        //     ],
        // ],

    ],

];
