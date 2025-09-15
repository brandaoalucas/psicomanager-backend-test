<?php

namespace App\BoundedContext\Task\Domain\Repository;

use App\BoundedContext\Task\Domain\Entity\Task;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository
{
    public function findById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $fields): Task
    {
        return Task::create($fields);
    }

    public function findPaginated(
        int $userId,
        int $page = 1,
        int $perPage = 15,
        ?string $status = null,
        ?string $search = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc'
    ): LengthAwarePaginator {

        $query = Task::where('user_id', $userId);


        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(
            perPage: $perPage,
            page: $page
        );
    }

    public function findByUser(int $userId): array
    {
        return Task::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function findByUserAndStatus(int $userId, string $status): array
    {
        return Task::where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

}
