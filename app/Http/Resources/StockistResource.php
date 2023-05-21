<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\UserRelationWithOther;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\isNull;

class StockistResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'userId' => $this->id,
            'userName' => $this->user_name,
            'pin' => $this->email,
            'password' => $this->visible_password,
            'userTypeId' => $this->user_type_id,
            'balance' => $this->closing_balance,
            'blocked' =>$this->blocked,
            'commission' => $this->commission,
            'superStockistId' => is_Null(UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first())? 'null': (UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first())->super_stockist_id,
//            'superStockistId' => is_Null(UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first())? 'null': (UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first()),
            'superStockistName' =>User::find((UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first())->super_stockist_id)->user_name,
            'superStockistPin' =>User::find((UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first())->super_stockist_id)->email,
            'superStockistBalance' =>User::find((UserRelationWithOther::whereStockistId($this->id)->whereActive(1)->first())->super_stockist_id)->closing_balance
//            'superStockistName' => (User::find(3))

        ];
    }
}
