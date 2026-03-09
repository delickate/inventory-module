<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Purchase_Return_Items extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'quantity' => $this->quantity,
'unit_price' => $this->unit_price,
'unit_price' => $this->unit_price,
'purchase_return_id' => $this->purchase_return_id,
'product_id' => $this->product_id,


        ];
    }
}

