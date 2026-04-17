<?php

namespace App\Repositories\Eloquent;

use App\Models\Todo;
use App\Models\User;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TodoRepository implements TodoRepositoryInterface
{
    public function paginateForUser(User $user, array $filters): LengthAwarePaginator
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        return $user->todos()
            ->when(isset($filters['search']), function ($query) use ($filters): void {
                $query->where(function ($nestedQuery) use ($filters): void {
                    $nestedQuery
                        ->where('title', 'like', '%'.$filters['search'].'%')
                        ->orWhere('description', 'like', '%'.$filters['search'].'%');
                });
            })
            ->when(array_key_exists('is_completed', $filters), function ($query) use ($filters): void {
                $query->where('is_completed', $filters['is_completed']);
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();
    }

    public function createForUser(User $user, array $attributes): Todo
    {
        return $user->todos()->create($attributes);
    }

    public function findForUser(User $user, int $todoId): ?Todo
    {
        return $user->todos()->whereKey($todoId)->first();
    }

    public function update(Todo $todo, array $attributes): Todo
    {
        $todo->update($attributes);

        return $todo->refresh();
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
    }
}
