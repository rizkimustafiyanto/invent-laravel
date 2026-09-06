<?php

namespace App\Modules\User\Services;

use App\Enums\UserRole;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Shared\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(
        protected UserRepositoryInterface $users
    ) {
    }

    public function all()
    {
        return $this->users->all();
    }

    public function paginate(int $perPage = 10)
    {
        return $this->users->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->users->find($id);
    }

    public function store(CreateUserDTO $data)
    {
        return $this->transaction(function () use ($data) {
            $user = $this->users->create($data->toArray());
            $this->audit('create', $user, [], $user->toArray());

            return $user;
        });
    }

    public function update(int $id, UpdateUserDTO $data)
    {
        return $this->transaction(function () use ($id, $data) {
            $user = $this->users->find($id);

            if (!$user) {
                return null;
            }

            $payload = array_filter(
                $data->toArray(),
                static fn ($value) => $value !== null
            );

            if ($payload === []) {
                return $user;
            }

            $updated = $this->users->update($id, $payload);

            if ($updated) {
                $this->audit('update', $updated, $user->toArray(), $updated->toArray());
            }

            return $updated;
        });
    }

    public function delete(int $id)
    {
        return $this->transaction(function () use ($id) {
            $user = $this->users->find($id);

            if (!$user) {
                return null;
            }

            $deleted = $this->users->delete($id);

            if ($deleted) {
                $this->audit('delete', $user, $user->toArray(), []);
            }

            return $deleted;
        });
    }

    public function optionRole(): array
    {
        return [
            [
                'label' => 'Member',
                'value' => UserRole::MEMBER->value,
            ],
            [
                'label' => 'Super Admin',
                'value' => UserRole::SUPER_ADMIN->value,
            ],
        ];
    }
}
