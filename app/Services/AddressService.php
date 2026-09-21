<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function store(User $user, array $data): Address
    {
        return DB::transaction(function () use ($user, $data): Address {
            if (! empty($data['is_default'])) {
                $user->addresses()->update(['is_default' => false]);
            }

            if ($user->addresses()->doesntExist()) {
                $data['is_default'] = true;
            }

            return $user->addresses()->create($data);
        });
    }

    public function update(User $user, Address $address, array $data): Address
    {
        abort_unless($address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $address, $data): Address {
            if (! empty($data['is_default'])) {
                $user->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }

            $address->update($data);

            return $address->refresh();
        });
    }

    public function setDefault(User $user, Address $address): Address
    {
        abort_unless($address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $address): Address {
            $user->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);

            return $address->refresh();
        });
    }

    public function destroy(User $user, Address $address): void
    {
        abort_unless($address->user_id === $user->id, 404);
        $address->delete();
    }

    public function formatted(Address $address): string
    {
        return collect([
            $address->address_line,
            $address->ward,
            $address->district,
            $address->province,
        ])->filter()->implode(', ');
    }
}
