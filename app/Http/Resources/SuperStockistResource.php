<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\UserRelationWithOther;
use Illuminate\Http\Resources\Json\JsonResource;

class SuperStockistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
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
            'commission' => $this->commission
        ];
    }
}
