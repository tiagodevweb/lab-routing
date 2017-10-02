<?php

declare(strict_types=1);

namespace Tdw\Routing\Method;

use Tdw\Routing\Contract\Method;

class POST implements Method
{
    public function __toString()
    {
        return 'POST';
    }
}
