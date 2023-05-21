<?php

namespace App\Http\Resources;

use App\Models\GameAllocation;
use App\Models\PayOutSlab;
use App\Models\User;

use App\Models\UserRelationWithOther;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StockistResource;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property mixed id
 * @property mixed user_name
 * @property mixed closing_balance
 * @property mixed email
 */
class TerminalResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'terminalId' => $this->id,
            'terminalName' => $this->user_name,
            'pin' => $this->email,
            'password' => $this->visible_password,
            'balance' => $this->closing_balance,
            'blocked' => $this->blocked,
            'commission' => $this->commission,
            'loginActivate' => $this->login_activate,
            'platform' => $this->platform,
            'version' => $this->version,
            'autoClaim' => $this->auto_claim,
            'status' => is_null(PersonalAccessToken::whereTokenableId($this->id)->first())? 'Offline': 'Online',
            'stockist' => new StockistResource(User::find((UserRelationWithOther::whereTerminalId($this->id)->whereActive(1)->first())->stockist_id)),
            'payoutSlabId' => $this->pay_out_slab_id,
            'stockistId' => (UserRelationWithOther::whereTerminalId($this->id)->whereActive(1)->first())->stockist_id,
            'superStockist' => new SuperStockistResource(User::find((UserRelationWithOther::whereTerminalId($this->id)->whereActive(1)->first())->super_stockist_id)),
            'superStockistId' => (UserRelationWithOther::whereTerminalId($this->id)->whereActive(1)->first())->super_stockist_id,
            'gamePermission' => GameAllocation::whereUserId($this->id)->first(),
        ];
    }
}
