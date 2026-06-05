<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WorkshopPhoto extends Model
{
    protected $fillable = ['filename', 'original_name'];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url('workshop/'.$this->filename),
        );
    }
}
