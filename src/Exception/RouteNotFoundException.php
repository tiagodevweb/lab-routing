<?php

namespace Tdw\Routing\Exception;

class RouteNotFoundException extends \Exception
{
    public $message = 'No matching route';
}
