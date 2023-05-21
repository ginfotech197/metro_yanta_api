<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockistToTerminal extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */
    private $terminal_id;
    /**
     * @var mixed
     */
    private $stockist_id;
}
