<?php

namespace App\Common\Enum;

use App\Common\Enum\HasFromSafely;
enum ReferenceType: string
{
    use HasFromSafely;
    case TASK = 'TASK';
    case USER = 'USER';

    public function label(): string
    {
        return match ($this) {
            self::TASK => 'Tarefa',
            self::USER => 'Usuário'
        };
    }

    public function getContextName(): string
    {
        return match ($this) {
            self::TASK => 'Task',
            SELF::USER => 'Iam'
        };
    }

}
