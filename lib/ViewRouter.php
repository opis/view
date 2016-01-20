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

use Closure;
use Serializable;
use Opis\Routing\Path;
use Opis\Routing\Router;
use Opis\Routing\PathFilter;
use Opis\Closure\SerializableClosure;
use Opis\Routing\Collections\FilterCollection;

class ViewRouter implements Serializable
{
    /** @var    array */
    protected $cache;

    /** @var    \Opis\Routing\Router */
    protected $router;

    /** @var    string */
    protected $viewKey;

    /** @var    \Opis\View\EngineResolver */
    protected $resolver;

    /** @var    boolean */
    protected $insertKey;

    /** @var    \Opis\View\RouteCollection */
    protected $collection;

    /** @var    \Opis\Routing\Collections\FilterCollection */
    protected $filters;
    
    /** @var    mixed|null  */
    protected $param;

    /**
     * Constructor
     * 
     * @param   \Opis\View\RouteCollection  $collection
     * @param   \Opis\View\EngineResolver   $resolver
     * @param   boolean                     $insertKey
     * @param   string                      $viewkey
     */
    public function __construct(RouteCollection $collection = null, EngineResolver $resolver = null, $insertKey = true, $viewkey = 'view')
    {
        if ($collection === null) {
            $collection = new RouteCollection();
        }

        if ($resolver === null) {
            $resolver = new EngineResolver();
        }

        $this->cache = array();
        $this->collection = $collection;
        $this->resolver = $resolver;
        $this->insertKey = (bool) $insertKey;
        $this->viewKey = (string) $viewkey;
    }

    /**
     * Get filters
     * 
     * @return  \Opis\Routing\Collections\FilterCollection
     */
    protected function getFilters()
    {
        if ($this->filters === null) {
            $filters = new FilterCollection();
            $filters[] = new PathFilter();
            $filters[] = new UserFilter();
            $this->filters = $filters;
        }

        return $this->filters;
    }

    /**
     * Get router
     * 
     * @return  \Opis\Routing\Router
     */
    protected function getRouter()
    {
        if ($this->router === null) {
            $this->router = new Router($this->collection, null, $this->getFilters());
        }

        return $this->router;
    }

    /**
     * Get the collection of routes
     * 
     * @return  \Opis\View\RouteCollection
     */
    public function routeCollection()
    {
        return $this->collection;
    }

    /**
     * Get the engine resolver instance
     * 
     * @return  \Opis\View\EngineResolver
     */
    public function engineResolver()
    {
        return $this->resolver;
    }

    /**
     * Register a new view
     * 
     * @param   string      $pattern
     * @param   callable    $resolver
     * @param   int         $priority
     * 
     * @return \Opis\View\Route
     */
    public function handle($pattern, $resolver, $priority = 0)
    {
        $route = new Route($pattern, $resolver, $priority);
        $this->collection[] = $route;
        $this->cache = array(); //clear cache
        return $route;
    }

    /**
     * Render a view
     * 
     * @param   \Opis\View\ViewableInterface|mixed  $view
     * 
     * @return  mixed
     */
    public function render($view)
    {
        if (!($view instanceof ViewableInterface)) {
            return $view;
        }

        $path = $this->resolveViewName($view->viewName());

        if ($path === null) {
            return null;
        }

        $engine = $this->resolver->resolve($path, $this->param);

        $arguments = $view->viewArguments();

        if ($this->insertKey) {
            $arguments[$this->viewKey] = $this;
        }

        return $engine->build($path, $arguments);
    }

    /**
     * Render a view
     * 
     * @param   string  $name
     * @param   array   $arguments
     * 
     * @return  mixed
     */
    public function renderView($name, array $arguments = array())
    {
        return $this->render(new View($name, $arguments));
    }

    /**
     * Resolve a view's name
     * 
     * @param   string  $name
     * 
     * @return  string
     */
    public function resolveViewName($name)
    {
        if (!isset($this->cache[$name])) {
            $this->collection->sort();
            $this->cache[$name] = $this->getRouter()->route(new Path($name));
        }

        return $this->cache[$name];
    }

    /**
     * Serialize
     * 
     * @return  string
     */
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

    /**
     * Unserialize
     * 
     * @param   string   $data
     */
    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);

        $this->resolver = $object['resolver'];
        $this->collection = $object['collection'];
        $this->insertKey = $object['insertKey'];
        $this->viewKey = $object['viewKey'];
    }
}
