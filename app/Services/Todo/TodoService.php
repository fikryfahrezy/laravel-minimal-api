<?php

namespace App\Services\Todo;

use App\Models\Todo;
use App\Models\User;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TodoService
{
    public function __construct(private readonly TodoRepositoryInterface $todos) {}

    public function list(User $user, array $filters): LengthAwarePaginator
    {
        return $this->todos->paginateForUser($user, $filters);
    }

    public function create(User $user, array $attributes): Todo
    {
        return $this->todos->createForUser($user, $attributes);
    }

    public function show(User $user, int $todoId): Todo
    {
        return $this->findOrFail($user, $todoId);
    }

    public function update(User $user, int $todoId, array $attributes): Todo
    {
        return $this->todos->update($this->findOrFail($user, $todoId), $attributes);
    }

    public function delete(User $user, int $todoId): void
    {
        $this->todos->delete($this->findOrFail($user, $todoId));
    }

    private function findOrFail(User $user, int $todoId): Todo
    {
        $todo = $this->todos->findForUser($user, $todoId);

        if (! $todo) {
            throw (new ModelNotFoundException)->setModel(Todo::class, [$todoId]);
        }

        return $todo;
    }
}
