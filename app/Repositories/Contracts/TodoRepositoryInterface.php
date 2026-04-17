<?php

namespace App\Repositories\Contracts;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TodoRepositoryInterface
{
    public function paginateForUser(User $user, array $filters): LengthAwarePaginator;

    public function createForUser(User $user, array $attributes): Todo;

    public function findForUser(User $user, int $todoId): ?Todo;

    public function update(Todo $todo, array $attributes): Todo;

    public function delete(Todo $todo): void;
}
