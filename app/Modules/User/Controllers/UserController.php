<?php

namespace App\Modules\User\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends BaseController
{
    public function __construct(
        protected UserService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $paginator = $this->service->paginate($request->integer('limit', 10));

        return $this->paginatedResponse(
            UserResource::collection($paginator),
            $paginator,
            'Users retrieved successfully'
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validated();
        $user = $this->service->store(new CreateUserDTO(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role: isset($validated['role']) ? UserRole::from($validated['role']) : null,
        ));

        return $this->success(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    public function show(int $user): JsonResponse
    {
        $user = $this->service->find($user);

        if (!$user) {
            return $this->error('User not found', status: 404);
        }

        Gate::authorize('view', $user);

        return $this->success(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, int $user): JsonResponse
    {
        $target = $this->service->find($user);

        if (!$target) {
            return $this->error('User not found', status: 404);
        }

        Gate::authorize('update', $target);

        $actor = $request->user();
        $payload = $request->validated();

        $actorRole = $actor?->role instanceof UserRole ? $actor->role->value : $actor?->role;
        if ($actorRole !== UserRole::SUPER_ADMIN->value) {
            unset($payload['role']);
        }

        $updated = $this->service->update($user, new UpdateUserDTO(
            name: $payload['name'] ?? null,
            email: $payload['email'] ?? null,
            password: $payload['password'] ?? null,
            role: isset($payload['role']) ? UserRole::from($payload['role']) : null,
        ));

        return $this->success(
            new UserResource($updated),
            'User updated successfully'
        );
    }

    public function destroy(int $user): JsonResponse
    {
        $target = $this->service->find($user);

        if (!$target) {
            return $this->error('User not found', status: 404);
        }

        Gate::authorize('delete', $target);

        $deleted = $this->service->delete($user);

        if (!$deleted) {
            return $this->error('User not found', status: 404);
        }

        return $this->success(message: 'User deleted successfully');
    }

    public function optionrole(): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        return $this->success(
            $this->service->optionRole(),
            'Roles retrieved successfully'
        );
    }

}
