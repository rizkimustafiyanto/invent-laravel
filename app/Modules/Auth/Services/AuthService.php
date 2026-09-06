<?php

namespace App\Modules\Auth\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Repositories\Contracts\AuthRepositoryInterface;
use App\Modules\Shared\Services\BaseService;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService extends BaseService
{
    public function __construct(
        protected AuthRepositoryInterface $auths
    ) {
    }

    public function login(LoginDTO $data): ?array
    {
        $user = $this->auths->verifyCredentials($data->email, $data->password);

        if (!$user) {
            return null;
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function register(RegisterDTO $data): array
    {
        return $this->transaction(function () use ($data) {
            /** @var User $user */
            $user = $this->auths->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
                'role' => UserRole::MEMBER->value,
            ]);

            return [
                'user' => $user,
                'token' => $user->createToken('auth-token')->plainTextToken,
            ];
        });
    }

    public function me(User $user): User
    {
        return $user;
    }

    public function logout(User $user): bool
    {
        $currentToken = $user->currentAccessToken();

        if ($currentToken instanceof PersonalAccessToken) {
            $currentToken->delete();
        }

        return true;
    }
}
