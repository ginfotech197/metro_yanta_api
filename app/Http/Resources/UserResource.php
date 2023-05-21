<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed email
 * @property mixed id
 * @property mixed user_type
 * @property mixed closing_balance
 * @property mixed user_name
 * @property mixed visible_password
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'userId' => $this->id,
            'userName' => $this->user_name,
            'pin' => $this->email,
            'password' => $this->visible_password,
            'userTypeName' => ($this->user_type)->user_type_name,
            'userTypeId' => ($this->user_type)->id,
            'balance' => $this->closing_balance,
            'blocked' => $this->blocked,
            'commission' => $this->commission,
            'stockistId' => $this->stockist_to_terminal,
        ];
    }
}
