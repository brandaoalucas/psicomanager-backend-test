<?php

namespace App\Common\ValueObjects;

use App\Common\Serializer\DTOJsonSerializer;

trait StringifyObjectVars
{
    public function __toString(): string
    {
        return DTOJsonSerializer::toString($this);
    }
}
