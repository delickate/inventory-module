<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Purchase_Returns extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'return_date' => $this->return_date,
'total_amount' => $this->total_amount,
'reason' => $this->reason,
'purchase_id' => $this->purchase_id,
'supplier_id' => $this->supplier_id,


        ];
    }
}

