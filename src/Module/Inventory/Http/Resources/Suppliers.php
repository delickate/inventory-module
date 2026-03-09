<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Suppliers extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'name' => $this->name,
'email' => $this->email,
'address' => $this->address,


        ];
    }
}

