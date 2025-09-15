<?php

namespace App\BoundedContext\Task\Application\Query\GetPaginatedTasks;

use App\BoundedContext\Task\Domain\Repository\TaskRepository;
use App\BoundedContext\Task\Domain\Entity\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GetPaginatedTasksQueryHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(GetPaginatedTasksQuery $query): LengthAwarePaginator
    {
        $sqlQuery = Task::query()
            ->leftJoin('users', 'users.id', '=', 'tasks.user_id')
            ->select('tasks.*')
            ->addSelect([
                'users.name as user_name',
                'users.email as user_email',
            ])
            ->addSelect([
                DB::raw('DATEDIFF(NOW(), tasks.created_at) as days_since_created'),
                DB::raw('CASE
                    WHEN tasks.status = "completed" THEN DATEDIFF(tasks.updated_at, tasks.created_at)
                    ELSE DATEDIFF(NOW(), tasks.created_at)
                END as days_to_complete'),
            ])
            ->where('tasks.user_id', $query->loggedUser->id);

        $this->applyFilters($sqlQuery, $query);

        $this->applySorting($sqlQuery, $query);

        $paginator = $sqlQuery->paginate(
            perPage: $query->perPage,
            page: $query->page
        );

        $paginator->getCollection()->transform(function ($row) {
            return $this->mapResult($row);
        });

        return $paginator;
    }

    private function applyFilters($sqlQuery, GetPaginatedTasksQuery $query): void
    {
        // Filtro por status
        if ($query->status) {
            $sqlQuery->where('tasks.status', $query->status);
        }

        if ($query->search) {
            $sqlQuery->where(function ($nested) use ($query) {
                $nested->where('tasks.title', 'like', '%' . $query->search . '%')
                       ->orWhere('tasks.description', 'like', '%' . $query->search . '%');
            });
        }
    }

    private function applySorting($sqlQuery, GetPaginatedTasksQuery $query): void
    {
        $sortMapping = [
            'created_at' => 'tasks.created_at',
            'updated_at' => 'tasks.updated_at',
            'title' => 'tasks.title',
            'status' => 'tasks.status',
            'user_name' => 'users.name',
            'days_since_created' => 'days_since_created',
        ];

        $sortField = $sortMapping[$query->sortBy] ?? 'tasks.created_at';
        $sqlQuery->orderBy($sortField, $query->sortOrder);
    }

    private function mapResult($row): object
    {
        $hiddenFields = (new Task())->getHidden();
        $result = (object) collect((array) $row)->except($hiddenFields)->all();

        $result->status_label = $this->getStatusLabel($result->status);
        $result->created_at_formatted = $result->created_at ?
            \Carbon\Carbon::parse($result->created_at)->diffForHumans() : null;
        $result->days_since_created_text = $result->days_since_created . ' dias';

        $result->is_overdue = $this->isOverdue($result);
        $result->priority = $this->calculatePriority($result);

        return $result;
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Pendente',
            'in_progress' => 'Em Progresso',
            'completed' => 'Concluída',
            default => $status,
        };
    }

    private function isOverdue($task): bool
    {
        return $task->status === 'pending' && $task->days_since_created > 7;
    }

    private function calculatePriority($task): string
    {
        if ($task->days_since_created > 14) return 'high';
        if ($task->days_since_created > 7) return 'medium';
        return 'low';
    }
}
