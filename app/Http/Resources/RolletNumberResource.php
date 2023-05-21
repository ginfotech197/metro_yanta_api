<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RolletNumberResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'rolletNumberId' => $this->id,
            'rolletNumber' => $this->rollet_number,
            // 'numberCombinations' => NumberCombinationSimpleResource::collection($this->number_combinations)
        ];
    }
}
