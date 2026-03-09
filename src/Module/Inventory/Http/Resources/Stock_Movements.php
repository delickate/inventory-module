<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Stock_Movements extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'quantity' => $this->quantity,
'type' => $this->type,
'reason' => $this->reason,
'reference_id' => $this->reference_id,
'warehouse_id' => $this->warehouse_id,
'product_id' => $this->product_id,


        ];
    }
}

