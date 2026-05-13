<?php

namespace App\Models;

use Database\Factories\TableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    /** @use HasFactory<TableFactory> */
    use HasFactory, SoftDeletes;

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
