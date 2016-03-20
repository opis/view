<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
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

use Opis\Routing\Pattern;
use Opis\Routing\Compiler;
use Opis\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    /** @var    int */
    protected $priority;

    /**
     * Constructor
     * 
     * @param   string      $pattern
     * @param   callable    $action
     * @param   int         $priority   (optional)
     */
    public function __construct($pattern, $action, $priority = 0)
    {
        $this->priority = $priority;
        parent::__construct(new Pattern($pattern), $action);
    }

    /**
     * Get the compiler
     * 
     * @staticvar   \Opis\Routing\Compiler  $compiler
     * 
     * @return      \Opis\Routing\Compiler
     */
    public function getCompiler()
    {
        static $compiler = null;

        if ($compiler === null) {
            $compiler = new Compiler('{', '}', '.', '?', (Compiler::CAPTURE_LEFT | Compiler::CAPTURE_TRAIL), '`', 'u', '[a-zA-Z0-9\/\,\-_%=]+');
        }

        return $compiler;
    }

    /**
     * Get route's priority
     * 
     * @return  int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * A more suggestive alias of the `wildcard` method
     * 
     * @param   string  $name
     * @param   string  $value
     * 
     * @return  string
     */
    public function where($name, $value)
    {
        return $this->wildcard($name, $value);
    }
}
