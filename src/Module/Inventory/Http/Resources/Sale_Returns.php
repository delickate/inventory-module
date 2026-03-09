<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Sale_Returns extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'return_date' => $this->return_date,
'total_amount' => $this->total_amount,
'reason' => $this->reason,
'sale_id' => $this->sale_id,
'customer_id' => $this->customer_id,


        ];
    }
}

