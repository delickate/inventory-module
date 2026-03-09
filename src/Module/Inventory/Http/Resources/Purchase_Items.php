<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Purchase_Items extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'purchase_id' => $this->purchase_id,
'product_id' => $this->product_id,
'quantity' => $this->quantity,
'unit_price' => $this->unit_price,
'subtotal' => $this->subtotal,


        ];
    }
}

