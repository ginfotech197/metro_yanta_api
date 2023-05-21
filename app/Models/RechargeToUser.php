<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargeToUser extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */
    private $beneficiary_uid;
    /**
     * @var mixed
     */
    private $recharge_done_by_uid;
    /**
     * @var mixed
     */
    private $amount;

    protected $hidden = [
        "updated_at"
    ];
}
