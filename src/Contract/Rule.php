<?php

declare(strict_types=1);

namespace Tdw\Routing\Contract;

interface Rule
{
    /**
     * @return string
     */
    public function asRegex(): string;
}
