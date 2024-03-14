<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class search_histories extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_value', 'created_by','created_at'
    ];
}
