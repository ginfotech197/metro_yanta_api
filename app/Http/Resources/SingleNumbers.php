<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed single_number
 * @property mixed number_combinations
 */
class SingleNumbers extends JsonResource
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
            'singleNumberId' => $this->id,
            'singleName' => $this->single_name,
            'singleNumber' => $this->single_number,
            // 'numberCombinations' => NumberCombinationSimpleResource::collection($this->number_combinations)
        ];
    }
}
