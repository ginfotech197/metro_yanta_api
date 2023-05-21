<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed draw_name
 * @property mixed start_time
 * @property mixed end_time
 * @property mixed active
 * @property mixed visible_time
 */
class DrawMasterResource extends JsonResource
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
            'drawId' => $this->id,
            'drawName' => $this->draw_name,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
            'visibleTime' => $this->visible_time,
            'gameId' => $this->game_id,
            'active' => $this->active
        ];
    }
}
