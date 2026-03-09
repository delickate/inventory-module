<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Products extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'name' => $this->name,
'sku' => $this->sku,
'barcode' => $this->barcode,
'category_id' => $this->category_id,
'unit_id' => $this->unit_id,
'cost_price' => $this->cost_price,
'sale_price' => $this->sale_price,
'description' => $this->description,
'image' => $this->image,
'status' => $this->status,


        ];
    }
}

