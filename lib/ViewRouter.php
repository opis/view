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
use Serializable;
use Opis\Routing\Path;
use Opis\Routing\Router;
use Opis\Routing\PathFilter;
use Opis\Closure\SerializableClosure;
use Opis\Routing\Collections\FilterCollection;

class ViewRouter implements Serializable
{
    
    protected $cache;
    
    protected $router;
    
    protected $viewKey;
    
    protected $resolver;
    
    protected $insertKey;
    
    protected $collection;
    
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
        
        $filters = new FilterCollection();
        $filters[] = new PathFilter();
        $filters[] = new UserFilter();
        
        $this->cache = array();
        $this->collection = $collection;
        $this->resolver = $resolver;
        $this->insertKey = (bool) $insertKey;
        $this->viewKey = (string) $viewkey;
        $this->router = new Router($this->collection, null, $filters);
    }
    
    public function routeCollection()
    {
        return $this->collection;
    }
    
    public function engineResolver()
    {
        return $this->resolver;
    }
    
    public function handle($pattern, $resolver, $priority = 0)
    {
        $route = new Route($pattern, $resolver, $priority);
        $this->collection[] = $route;
        $this->cache = array(); //clear cache
        return $route;
    }
    
    public function render($view)
    {
        if(!($view instanceof ViewableInterface))
        {
            return $view;
        }
        
        $path = $this->resolveViewName($view->viewName());
        
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
        return $this->render(new View($name, $arguments));
    }
    
    public function resolveViewName($name)
    {
        if(!isset($this->cache[$name]))
        {
            $this->collection->sort();
            $this->cache[$name] = $this->router->route(new Path($name));
        }
        
        return $this->cache[$name];
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
        
        $filters = new FilterCollection();
        $filters[] = new PathFilter();
        $filters[] = new UserFilter();
        
        $this->router = new Router($this->collection, null, $filters);
    }
    
}
