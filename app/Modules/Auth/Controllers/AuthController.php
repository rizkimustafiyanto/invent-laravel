<?php

namespace App\Modules\Auth\Controllers;

use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\AuthTokenResource;
use App\Modules\Auth\Resources\AuthUserResource;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(
        protected AuthService $service
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login(new LoginDTO(
            ...$request->validated()
        ));

        if (!$result) {
            return $this->error('Invalid credentials', status: 401);
        }

        return $this->success(new AuthTokenResource($result), 'Login successful');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->service->register(new RegisterDTO(
            ...$request->validated()
        ));

        return $this->success(new AuthTokenResource($result), 'Register successful', 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', status: 401);
        }

        return $this->success(new AuthUserResource($this->service->me($user)), 'Profile retrieved successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', status: 401);
        }

        $this->service->logout($user);

        return $this->success(message: 'Logged out successfully');
    }
}
