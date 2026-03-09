<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Purchases extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'supplier_id' => $this->supplier_id,
'invoice_no' => $this->invoice_no,
'total_amount' => $this->total_amount,


        ];
    }
}

