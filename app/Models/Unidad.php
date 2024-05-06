<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    // Relación para obtener el padre de la unidad actual
    public function padre()
    {
        return $this->belongsTo(Unidad::class, 'padre_id');
    }

    // Función para obtener el camino hasta el padre de la unidad actual
    public function getPathToParentAttribute()
    {
        $path = $this->nombre . ' - ' . $this->sigla;
        $unidad = $this;

        $ancestors = [];

        while ($unidad->padre) {
            array_push($ancestors, $unidad->padre->nombre . ' - ' . $unidad->padre->sigla);
            $unidad = $unidad->padre;
        }

        return $path . ' > ' . implode(' > ', $ancestors);
    }
}
