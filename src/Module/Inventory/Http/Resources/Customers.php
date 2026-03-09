<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Customers extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'name' => $this->name,
'phone' => $this->phone,
'address' => $this->address,


        ];
    }
}

