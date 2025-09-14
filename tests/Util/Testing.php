<?php

namespace Tests\Util;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
#[Attribute(Attribute::IS_REPEATABLE)]
class Testing
{
    public function __construct(string $class) {}
}
