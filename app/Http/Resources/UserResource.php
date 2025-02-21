<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    private $token;

    public function withToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'roles'     => $this->roles->pluck('name'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'token'     => $this->when($this->token, $this->token) // ✅ إرجاع التوكن فقط عند وجوده
        ];
    }
}
