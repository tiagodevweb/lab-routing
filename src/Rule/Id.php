<?php

declare(strict_types=1);

namespace Tdw\Routing\Rule;

use Tdw\Routing\Contract\Rule;

class Id implements Rule
{
    public function __toString()
    {
        return '[0-9]+';
    }
}
