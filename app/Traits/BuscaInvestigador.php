<?php

namespace App\Traits;

use App\Models\Investigador;
use Carbon\Carbon;

trait BuscaInvestigador
{
    public function buscarInvestigador($term)
    {
        $resultados = Investigador::whereHas('persona', function($query) use ($term) {
            $query->where('apellido', 'like', '%' . $term . '%')
                ->orWhere('cuil', 'like', '%' . $term . '%');
        })
            ->with([
                'persona:id,apellido,nombre,cuil,email,nacimiento',
                'titulos:id,nombre',
                'tituloposts:id,nombre',
                'cargos:id,nombre',
                'carrerainvs:id,nombre',
                'categorias:id,nombre',
                'sicadis:id,nombre',
                'becas:id,institucion,beca,desde,hasta'
            ])
            ->get();

        return $resultados->map(function($investigador) {
            $titulo = $investigador->titulos->first();
            $titulopost = $investigador->tituloposts->first();
            $cargo = $investigador->cargos()
                ->where('activo', true)
                ->orderBy('deddoc', 'asc')
                ->orderBy('orden', 'asc')
                ->first();

            $carrerainv = $investigador->carrerainvs()->where('actual', true)->first();
            $sicadi = $investigador->sicadis()->where('actual', true)->first();
            $categoria = $investigador->categorias()->where('actual', true)->first();
            $hoy = Carbon::today();
            $beca = $investigador->becas()
                ->where('desde', '<=', $hoy)
                ->where('hasta', '>=', $hoy)
                ->first();

            return [
                'id' => $investigador->id,
                'apellido' => $investigador->persona->apellido,
                'nombre' => $investigador->persona->nombre,
                'cuil' => $investigador->persona->cuil,
                'email' => $investigador->persona->email,
                'nacimiento' => $investigador->persona->nacimiento,
                // resto igual...
            ];
        });
    }
}
