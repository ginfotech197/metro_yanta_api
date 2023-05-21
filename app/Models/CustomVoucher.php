<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomVoucher extends Model
{
    use HasFactory;

    /**
     * @var mixed|string
     */
    private $voucher_name;
    /**
     * @var float|int|mixed
     */
    private $accounting_year;
    /**
     * @var mixed|string
     */
    private $delimiter;
    /**
     * @var mixed|string
     */
    private $prefix;
    /**
     * @var int|mixed
     */
    private $last_counter;

}
