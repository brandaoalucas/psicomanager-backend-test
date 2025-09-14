<?php

namespace App\BoundedContext\AuditLog\Domain\Entity;

use App\BoundedContext\AuditLog\Domain\EntityFactory\AuditLogFactory;
use App\Common\Enum\ReferenceType;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $reference_id
 * @property int $user_id
 * @property ReferenceType $reference_type
 * @property string $description
 */
class AuditLog extends Model
{
    public $fillable = [
        'id',
        'reference_type',
        'reference_id',
        'user_id',
        'description',
    ];

    protected $casts = [
        'reference_type' => ReferenceType::class,
    ];

    public static function factory(): AuditLogFactory
    {
        return AuditLogFactory::new();
    }
}
