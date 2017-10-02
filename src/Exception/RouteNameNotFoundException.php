<?php

namespace Tdw\Routing\Exception;

class RouteNameNotFoundException extends \Exception
{
    public $message = 'No route matches this name';
}
