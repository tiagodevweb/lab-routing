<?php

declare(strict_types=1);

namespace Tdw\Routing\Method;

use Tdw\Routing\Contract\Method;

class GET implements Method
{
    public function __toString()
    {
        return 'GET';
    }
}
