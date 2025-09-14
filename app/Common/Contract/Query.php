<?php

namespace App\Common\Contract;

use App\Common\ValueObjects\StringifyObjectVars;
use Stringable;

abstract class Query implements Stringable
{
    use StringifyObjectVars;
}
