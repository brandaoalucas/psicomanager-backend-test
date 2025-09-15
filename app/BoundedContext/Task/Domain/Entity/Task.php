<?php

namespace App\BoundedContext\Task\Domain\Entity;

use App\BoundedContext\Task\Domain\EntityFactory\TaskFactory;
use App\BoundedContext\Task\Domain\Enum\TaskStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use App\Common\Casts\CastType;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property TaskStatusEnum $status
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property CarbonImmutable $deleted_at
 */
class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
        'created_at' => CastType::IMMUTABLE_DATETIME,
    ];

    public static function factory(): TaskFactory
    {
        return TaskFactory::new();
    }
}
