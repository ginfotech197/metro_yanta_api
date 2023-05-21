<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberCombination extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = [
        "created_at","updated_at"
    ];

    public function single(){
        return $this->belongsTo(SingleNumber::class,'single_number_id');
    }
}
