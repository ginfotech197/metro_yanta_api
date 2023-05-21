<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use App\Http\Resources\TerminalResource;
use App\Http\Resources\DrawMasterResource;

/**
 * @property mixed barcode_number
 * @property mixed draw_master_id
 * @property mixed terminal_id
 * @property mixed user_id
 * @property mixed terminal
 * @property mixed draw_time
 * @property mixed created_at
 */
class PlayMasterResource extends JsonResource
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
            'barcodeNumber' => Str::substr($this->barcode_number,0,8),
//            'drawTime' => new DrawMasterResource($this->draw_time),
//            'terminal' => new TerminalResource($this->terminal),
            'ticketTakenTime' => $this->created_at
        ];
    }
}
