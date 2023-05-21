<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $hidden = [
        "inforce","created_at","updated_at",'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * @var mixed
     */
    private $closing_balance;
    /**
     * @var mixed
     */
    private $user_name;
    /**
     * @var mixed|string
     */
    private $email;
    /**
     * @var mixed|string
     */
    private $password;
    /**
     * @var int|mixed
     */
    private $user_type_id;
    /**
     * @var int|mixed
     */
    private $opening_balance;

    public function setPasswordAttribute($password)
    {
        if ( !empty($password) ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function user_type(){
        return $this->belongsTo(UserType::class,'user_type_id');
    }

    public function stockist_to_terminal(){
        return $this->hasOne(StockistToTerminal::class, 'terminal_id');
    }

    public function getStockistIdAttribute(){
        $stockistToTerminal= $this->stockist_to_terminal;
        if(!$stockistToTerminal){
            return null;
        }
        return $stockistToTerminal->stockist_id;
    }

    


}
