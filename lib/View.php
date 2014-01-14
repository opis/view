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

use Closure;
use Opis\Routing\Router;

class View
{
    
    protected $resolver;
    
    protected $collection;
    
    protected $insertKey;
    
    protected $viewKey;
    
    protected static $routerInstance;
    
    public function __construct(RouteCollection $routes, EngineResolver $resolver, $insertKey = true, $viewkey = 'view')
    {   
        $this->resolver = $resolver;
        $this->collection = $collection;
        $this->insertKey = (bool) $insertKey;
        $this->viewKey = (string) $viewkey;
    }
    
    protected function router()
    {
        if(static::$routerInstance === null)
        {
            static::$routerInstance = new Router($this->collection);
        }
        
        return static::$routerInstance;
    }
    
    public function handle($pattern, Closure $callback, $priority = 0)
    {
        return $this->collection->add(new ViewRoute($pattern, $callback), $priority);
    }
    
    public function render($view)
    {
        if(!($view instanceof ViewableInterface))
        {
            return $view;
        }
        
        $path = $this->router()->route($view);
        
        $engine = $this->resolver->resolve($path);
        
        $arguments = $view->arguments();
        
        if($this->insertKey)
        {
            $arguments[$this->viewKey] = $this;
        }
        
        return $engine->build($path, $arguments);
    }
    
}