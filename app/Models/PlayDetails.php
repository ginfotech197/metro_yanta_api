<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayDetails extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */
    private $play_master_id;
    /**
     * @var mixed
     */
    private $game_type_id;
    /**
     * @var mixed
     */
    private $mrp;
    /**
     * @var mixed
     */
    private $quantity;
    /**
     * @var mixed
     */
    private $number_combination_id;
    /**
     * @var mixed
     */
    private $commission;
    /**
     * @var mixed
     */
    private $payout;

    public function game(){
        return $this->belongsTo(GameType::class,'game_type_id');
    }
}
