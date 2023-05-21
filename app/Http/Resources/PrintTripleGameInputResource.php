<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrintTripleGameInputResource extends JsonResource
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
            'visibleTripleNumber' => $this->visible_triple_number,
            'quantity' => $this->quantity,
            'singleNumber' => $this->single_number
        ];
    }
}
