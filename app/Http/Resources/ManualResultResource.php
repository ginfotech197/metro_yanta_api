<?php

namespace App\Http\Resources;

use App\Http\Resources\NumberCombinationSimpleResource;
use App\Models\NumberCombination;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SingleNumber;
use App\Http\Resources\SingleNumberSimpleResource;

/**
 * @property mixed id
 * @property mixed draw_master_id
 * @property mixed number_combination_id
 * @property mixed game_date
 */
class ManualResultResource extends JsonResource
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
            'manualResultId'=> $this->id,
            'drawMaster'=> new DrawMasterResource($this->draw_master),
            'numberCombination'=> new NumberCombinationSimpleResource($this->number_combination),
            'single'=> new SingleNumberSimpleResource(SingleNumber::find(($this->number_combination->single_number_id))),
            'gameDate'=> $this->game_date,
        ];
    }
}
