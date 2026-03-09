<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Inventory extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'quantity' => $this->quantity,
'warehouse_id' => $this->warehouse_id,
'product_id' => $this->product_id,


        ];
    }
}

