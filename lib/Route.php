<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

use Closure;
use Opis\Routing\Route as BaseRoute;
use Opis\Routing\Pattern;
use Opis\Routing\Compiler;

class Route extends BaseRoute
{

    public function __construct($pattern, Closure $action, $priority = 0)
    {
        parent::__construct(new Pattern($pattern), $action);
        $this->set('priority', $priority);
    }
    
    public static function getCompiler()
    {
        static $compiler = null;
        
        if($compiler === null)
        {
            $compiler = new Compiler('{', '}', '.', '?', (Compiler::CAPTURE_LEFT|Compiler::CAPTURE_TRAIL));
        }
        
        return $compiler;
    }
    
    public function getPriority()
    {
        return $this->get('priority', 0);
    }
    
    public function where($name, $value)
    {
        return $this->wildcard($name, $value);
    }
}
