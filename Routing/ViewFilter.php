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

namespace Opis\View\Routing;

use Opis\Routing\Route;
use Opis\Routing\FilterInterface;
use Opis\Routing\Compiler;

class ViewFilter
{
    
    protected $compiler;
    
    protected $view;
    
    public function __construct($view)
    {
        $this->view = $view;
        $this->compiler = new Compiler('{', '}', ':', '?', (Compiler::CAPTURE_LEFT|Compiler::CAPTURE_TRAIL));
    }
    
    public function match(Route $route)
    {
        $expression = $this->compiler->expression($route->getPath(), $route->getWildcards());
        
        if(!preg_match($expression->delimit(), $this->view))
        {
            return false;
        }
        
        $values = $expression->extract($this->view, $route->getDefaults());
        
        foreach($route->getBindings() as $name => $check)
        {
            if(isset($values[$name]))
            {
                if($check($values[$name]) === false)
                {
                    return false;
                }
            }
        }
        
        $route->set('expression', $expression);
        
        return true;
    }
    
}