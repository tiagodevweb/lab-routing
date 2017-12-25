<?php

declare(strict_types=1);

namespace Tdw\Routing\Rule;

use Tdw\Routing\Contract\Rule;

class Id implements Rule
{
    public function asRegex(): string
    {
        return '[0-9]+';
    }
}
