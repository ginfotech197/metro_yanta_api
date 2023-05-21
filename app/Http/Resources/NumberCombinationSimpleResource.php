<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed triple_number
 * @property mixed visible_triple_number
 */
class NumberCombinationSimpleResource extends JsonResource
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
            'numberCombinationId' => $this->id,
            'tripleNumber' => $this->triple_number,
            'visibleTripleNumber' => $this->visible_triple_number
        ];
    }
}
