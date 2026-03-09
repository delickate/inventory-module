<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Sale_Items extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'quantity' => $this->quantity,
'unit_price' => $this->unit_price,
'subtotal' => $this->subtotal,
'sale_id' => $this->sale_id,
'product_id' => $this->product_id,


        ];
    }
}

