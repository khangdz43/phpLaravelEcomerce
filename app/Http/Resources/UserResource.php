<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'roles'      => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),
            'orders_count' => $this->when(isset($this->orders_count), $this->orders_count),
            'addresses_count' => $this->when(isset($this->addresses_count), $this->addresses_count),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
