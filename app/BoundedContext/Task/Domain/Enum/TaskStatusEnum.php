<?php

namespace App\BoundedContext\Task\Domain\Enum;

enum TaskStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::IN_PROGRESS => 'Em Progresso',
            self::COMPLETED => 'Concluída'
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
