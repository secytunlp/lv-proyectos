<?php

return [
    'Centro' => [
        ['nombre'=>'Director','gobierno'=>true,'orden'=>1],
        ['nombre'=>'Sub-Director','gobierno'=>true,'orden'=>2],
        ['nombre'=>'Consejo directivo','gobierno'=>true,'orden'=>3],
        ['nombre'=>'Miembro','gobierno'=>false,'orden'=>4],
    ],
    'Laboratorio' => [
        ['nombre'=>'Director','gobierno'=>true,'orden'=>1],
        ['nombre'=>'Consejo asesor','gobierno'=>true,'orden'=>2],
        ['nombre'=>'Miembro','gobierno'=>false,'orden'=>3],
    ],
    'Instituto' => [
        ['nombre'=>'Director','gobierno'=>true,'orden'=>1],
        ['nombre'=>'Sub-Director','gobierno'=>true,'orden'=>2],
        ['nombre'=>'Consejo directivo','gobierno'=>true,'orden'=>3],
        ['nombre'=>'Miembro','gobierno'=>false,'orden'=>4],
    ],
    'Unidad promocional de I/D' => [
        ['nombre'=>'Responsable','gobierno'=>false,'orden'=>1],
        ['nombre'=>'Miembro','gobierno'=>false,'orden'=>2],
    ],
];
