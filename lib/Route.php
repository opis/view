<?php

namespace Opis\View\Routing;

use Closure;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Pattern;

class Route extends BaseRoute
{
    protected static $compilerInstance;
    
    protected $priority;
    
    public function __construct($pattern, Closure $action, $priority = 0)
    {
        $this->priority = $priority;
        parent::__construct(new Pattern($pattern), $action, compiler());
    }
    
    protected static function compiler()
    {
        if(static::$compilerInstance === null)
        {
            static::$compilerInstance = new Compiler('{', '}', '.', '?', (Compiler::CAPTURE_LEFT|Compiler::CAPTURE_TRAIL));
        }
    }
    
    public function getPriority()
    {
        return $this->priority;
    }
    
    public function where($name, $value)
    {
        return $this->wildcard($name, $value);
    }
}