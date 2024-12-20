<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'path', 'is_main'];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
