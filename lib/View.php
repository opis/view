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

use Opis\Routing\Router;
use Opis\Routing\Path;

class View
{
    
    protected $resolver;
    
    protected $collection;
    
    protected $insertKey;
    
    protected $viewKey;
    
    protected $router;
    
    public function __construct(RouteCollection $collection, EngineResolver $resolver = null, $insertKey = true, $viewkey = 'view')
    {
        $this->collection = $collection;
        
        if($resolver === null)
        {
            $resolver = new EngineResolver();
        }
        
        $this->resolver = $resolver;
        $this->insertKey = (bool) $insertKey;
        $this->viewKey = (string) $viewkey;
        $this->router = new Router($this->collection);
    }
    
    public function render($view)
    {
        if(!($view instanceof ViewableInterface))
        {
            return $view;
        }
        
        $this->collection->sort();
        
        $path = $this->router->route(new Path($view->viewName()));
        
        if($path === null)
        {
            return null;
        }
        
        $engine = $this->resolver->resolve($path);
        
        $arguments = $view->viewArguments();
        
        if($this->insertKey)
        {
            $arguments[$this->viewKey] = $this;
        }
        
        return $engine->build($path, $arguments);
    }
    
}