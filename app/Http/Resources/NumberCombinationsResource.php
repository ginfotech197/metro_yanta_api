<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SingleNumbers;

/**
 * @property mixed id
 * @property mixed triple_number
 * @property mixed visible_triple_number
 * @property mixed single
 */
class NumberCombinationsResource extends JsonResource
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
            'visibleTripleNumber' => $this->visible_triple_number,
//            'single' => new SingleNumbers($this->single),
        ];
    }
}
