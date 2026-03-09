<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Sales extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'date' => $this->date,
'total_amount' => $this->total_amount,
'customer_id' => $this->customer_id,


        ];
    }
}

