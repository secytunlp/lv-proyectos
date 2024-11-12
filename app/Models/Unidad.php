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

    /**
     * Verifica si cualquiera de los IDs objetivo está en la jerarquía de esta unidad.
     *
     * @param array $targetIds
     * @return bool
     */
    public function isInHierarchy(array $targetIds): bool
    {
        $current = $this;

        // Recorre hacia arriba en la jerarquía
        while ($current) {
            if (in_array($current->id, $targetIds)) {
                return true;
            }

            $current = $current->padre;
        }

        return false;
    }

}
