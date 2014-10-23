<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2014 Marius Sarca
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
use Serializable;
use Opis\Routing\Router;
use Opis\Routing\Path;
use Opis\Closure\SerializableClosure;

class ViewRouter implements Serializable
{
    
    protected $resolver;
    
    protected $collection;
    
    protected $insertKey;
    
    protected $viewKey;
    
    protected $router;
    
    public function __construct(RouteCollection $collection = null, EngineResolver $resolver = null, $insertKey = true, $viewkey = 'view')
    {
        if($collection === null)
        {
            $collection = new RouteCollection();
        }
        
        if($resolver === null)
        {
            $resolver = new EngineResolver();
        }
        
        $this->collection = $collection;
        $this->resolver = $resolver;
        $this->insertKey = (bool) $insertKey;
        $this->viewKey = (string) $viewkey;
        $this->router = new Router($this->collection);
    }
    
    public function routeCollection()
    {
        return $this->collection;
    }
    
    public function engineResolver()
    {
        return $this->resolver;
    }
    
    public function handle($pattern, Closure $resolver, $priority = 0)
    {
        $route = new Route($pattern, $resolver, $priority);
        $this->collection[] = $route;
        return $route;
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
    
    public function renderView($name, array $arguments = array())
    {
        return $this->render(new BaseView($name, $arguments));
    }
    
    public function serialize()
    {
        SerializableClosure::enterContext();
        $object = serialize(array(
            'resolver' => $this->resolver,
            'collection' => $this->collection,
            'insertKey' => $this->insertKey,
            'viewKey' => $this->viewKey
        ));
        SerializableClosure::exitContext();
        
        return $object;
    }
    
    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);
        $this->resolver = $object['resolver'];
        $this->collection = $object['collection'];
        $this->insertKey = $object['insertKey'];
        $this->viewKey = $object['viewKey'];
        $this->router = new Router($this->collection);
    }
    
}
