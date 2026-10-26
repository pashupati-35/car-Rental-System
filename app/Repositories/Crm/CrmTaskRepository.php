<?php

namespace App\Repositories\Crm;

use App\Models\Crm\CrmTask;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class CrmTaskRepository extends BaseRepository implements CrmTaskRepositoryInterface
{
    public function __construct(CrmTask $model)
    {
        parent::__construct($model);
    }

    public function getTasksForRelated(string $relatedType, int $relatedId): Collection
    {
        return $this->model->where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->latest('due_date')
            ->get();
    }

    public function createTask(array $data): CrmTask
    {
        return $this->model->create($data);
    }

    public function completeTask(int $taskId): CrmTask
    {
        $task = $this->model->findOrFail($taskId);
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $task;
    }
}
