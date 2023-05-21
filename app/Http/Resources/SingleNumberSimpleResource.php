<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed single_number
 */
class SingleNumberSimpleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'singleNumberId' => $this->id,
            'singleNumber' => $this->single_number
        ];
    }
}
