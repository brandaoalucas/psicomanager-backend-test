<?php

namespace App\Common\Contract;

use App\Common\ValueObjects\StringifyObjectVars;
use Stringable;

abstract class Command implements Stringable
{
    use StringifyObjectVars;
}
