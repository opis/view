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

use Opis\View\Engines\PHPEngine;

class EngineResolver
{
    
    protected $resolvers = array();
    
    protected $defaultEngine;
    
    public function __construct()
    {
        $this->defaultEngine = new PHPEngine();
    }
    
    public function register($extension, Closure $resolver)
    {
        $extension = '`.+' . preg_quote('.' . $extension . '.php', '`') . '$`';
        
        $this->resolvers[$extension] = array(
            'resolver' => $resolver,
            'instance' => null,
        );
        
    }
    
    public function resolve($path)
    {
        foreach($this->resolvers as $key => &$engine)
        {
            if(preg_match($key, $path))
            {
                if($engine['instance'] === null)
                {
                    $engine['instance'] = $engine['resolver']();
                }
                
                return $engine['instance'];
            }
        }
        
        return $this->defaultEngine;
    }
    
}
