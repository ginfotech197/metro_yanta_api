<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RechargeToUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rechargedTo' => new UserResource(User::find($this->beneficiary_uid)),
            'rechargedby' => new UserResource(User::find($this->recharge_done_by_uid)),
            'oldAmount' => $this->old_amount,
            'rechargedAmount' => $this->amount,
            'newAmount' => $this->new_amount,
            'date' =>  $this->created_at->format('Y-m-d'),
            'time' =>  $this->created_at->hour > 12 ? ($this->created_at->format('H') - 12).':'.$this->created_at->format('i').':'.$this->created_at->format('s').' PM' : $this->created_at->format('H:i:s').' AM'
        ];
    }
}
