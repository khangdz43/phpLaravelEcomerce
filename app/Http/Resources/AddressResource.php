<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'recipient_name' => $this->recipient_name,
            'phone'          => $this->phone,
            'address_line'   => $this->address_line,
            'ward'           => $this->ward,
            'district'       => $this->district,
            'province'       => $this->province,
            'is_default'     => $this->is_default,
            'full_address'   => implode(', ', array_filter([
                $this->address_line,
                $this->ward,
                $this->district,
                $this->province,
            ])),
        ];
    }
}
