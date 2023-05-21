<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleNumber extends Model
{
    use HasFactory;

    public function number_combinations(){
        return $this->hasMany(NumberCombination::class,'single_number_id');
    }

    protected $hidden = [
        "inforce","created_at","updated_at",
    ];
}
