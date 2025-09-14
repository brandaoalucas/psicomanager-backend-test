<?php

namespace App\Common\Enum;

enum ReferenceType: string
{
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
