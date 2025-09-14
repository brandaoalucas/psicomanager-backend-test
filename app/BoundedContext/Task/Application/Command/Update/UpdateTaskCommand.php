<?php

namespace App\BoundedContext\Task\Application\Command\Update;

use App\BoundedContext\Iam\Domain\Entity\User;
use App\Common\Contract\Command;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateTaskCommand extends Command
{
    public function __construct(
        public int      $id,
        public string   $title,
        public string   $description,
        public User     $loggedUser,
    ) {}
}
