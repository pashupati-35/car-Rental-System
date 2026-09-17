<?php

namespace App\Repositories\Crm;

use App\Models\Crm\CrmTask;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface CrmTaskRepositoryInterface extends BaseRepositoryInterface
{
    public function getTasksForRelated(string $relatedType, int $relatedId): Collection;

    public function createTask(array $data): CrmTask;

    public function completeTask(int $taskId): CrmTask;
}
