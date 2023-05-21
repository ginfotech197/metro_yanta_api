<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaharNumber extends Model
{
    use HasFactory;
    protected $hidden = [
        "inforce","created_at","updated_at",
    ];
}
