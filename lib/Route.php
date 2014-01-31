<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\View;

use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Pattern;
use Opis\Routing\Compiler;

class Route extends BaseRoute
{
    protected static $compilerInstance;
    
    protected $priority;
    
    public function __construct($pattern, callable $action, $priority = 0)
    {
        $this->priority = $priority;
        parent::__construct(new Pattern($pattern), $action, static::compiler());
    }
    
    protected static function compiler()
    {
        if(static::$compilerInstance === null)
        {
            static::$compilerInstance = new Compiler('{', '}', '.', '?', (Compiler::CAPTURE_LEFT|Compiler::CAPTURE_TRAIL));
        }
        
        return static::$compilerInstance;
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