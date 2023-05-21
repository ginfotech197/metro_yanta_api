<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualResult extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */
    private $number_combination_id;
    /**
     * @var mixed
     */
    private $draw_master_id;
    /**
     * @var \Illuminate\Support\Carbon|mixed
     */
    private $game_date;

    public function draw_master(){
        return $this->belongsTo(DrawMaster::class,'draw_master_id');
    }

    public function number_combination(){
        return $this->belongsTo(NumberCombination::class,'number_combination_id');
    }
}
