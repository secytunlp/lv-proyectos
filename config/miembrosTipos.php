<?php

return [
    'Centro' => [
        ['nombre'=>'Director','gobierno'=>true,'externo'=>false,'orden'=>1],
        ['nombre'=>'Sub-Director','gobierno'=>true,'externo'=>false,'orden'=>2],
        ['nombre'=>'Consejo de dirección','gobierno'=>true,'externo'=>false,'orden'=>3],
        ['nombre'=>'Miembro','gobierno'=>false,'externo'=>false,'orden'=>4],
        ['nombre'=>'Investigador correspondiente','gobierno'=>false,'externo'=>true,'orden'=>5],
    ],
    'Laboratorio' => [
        ['nombre'=>'Director','gobierno'=>true,'externo'=>false,'orden'=>1],
        ['nombre'=>'Sub-Director','gobierno'=>true,'externo'=>false,'orden'=>2],
        ['nombre'=>'Consejo de dirección','gobierno'=>true,'externo'=>false,'orden'=>3],
        ['nombre'=>'Miembro','gobierno'=>false,'externo'=>false,'orden'=>4],
        ['nombre'=>'Investigador correspondiente','gobierno'=>false,'externo'=>true,'orden'=>5],
    ],
    'Instituto' => [
        ['nombre'=>'Director','gobierno'=>true,'externo'=>false,'orden'=>1],
        ['nombre'=>'Sub-Director','gobierno'=>true,'externo'=>false,'orden'=>2],
        ['nombre'=>'Consejo de dirección','gobierno'=>true,'externo'=>false,'orden'=>3],
        ['nombre'=>'Miembro','gobierno'=>false,'externo'=>false,'orden'=>4],
        ['nombre'=>'Investigador correspondiente','gobierno'=>false,'externo'=>true,'orden'=>5],
    ],
    'Unidad promocional de I/D' => [
        ['nombre'=>'Responsable','gobierno'=>false,'orden'=>1],
        ['nombre'=>'Miembro','gobierno'=>false,'orden'=>2],
    ],
];
