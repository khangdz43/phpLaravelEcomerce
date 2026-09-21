<?php

namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $customerRole = Role::where('name', 'customer')->first();

            if ($customerRole) {
                $user->roles()->sync([$customerRole->id]);
            }

            return [
                'user' => $this->userData($user),
                // id|token 
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
            ];
        });
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $user->load('roles.permissions');

        return [
            'user' => $this->userData($user, true),
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(User $user): void
    {
        if (! $user) {
            return;
        }

        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();
        $token?->delete();
    }
    public function currentUser(User $user): array
    {
        $user->load('roles.permissions');

        $permissions = $user->roles
            ->flatMap(fn(Role $role) => $role->permissions)
            ->pluck('name')
            ->unique()
            ->values();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $permissions,
        ];
    }

    private function userData(User $user, bool $withRoles = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        if ($withRoles) {
            $data['roles'] = $user->roles->pluck('name');
        }

        return $data;
    }
}
