<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Titulo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','nivel','universidad_id'];


    /**
     * Define la relación con la facultad.
     *
     * @return BelongsTo
     */
    public function universidad(): BelongsTo
    {
        return $this->belongsTo('App\Models\Universidad');
    }

    public function getFullNameAttribute()
    {
        return $this->nombre . ' (' . $this->universidad->nombre.')';
    }
}
