<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AndarResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'andarNumberId' => $this->id,
            'andarNumber' => $this->andar_number
        ];
    }
}
