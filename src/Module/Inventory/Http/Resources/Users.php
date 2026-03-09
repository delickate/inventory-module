<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class Users extends JsonResource
{
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
'name' => $this->name,
'picture' => $this->picture,
'phone' => $this->phone,
'email' => $this->email,
'email_verified_at' => $this->email_verified_at,
'password' => $this->password,
'remember_token' => $this->remember_token,


        ];
    }
}

