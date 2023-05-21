<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrawMaster extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = [
        "created_at","updated_at"
    ];

    public function result_masters(){
        return $this->hasMany(ResultMaster::class,'draw_master_id');
    }
    public function manual_results(){
        return $this->hasMany(ManualResult::class,'draw_master_id');
    }
}
