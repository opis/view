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

use Opis\Routing\Path;
use Opis\Routing\Route;
use Serializable;
use Opis\Routing\Router;
use Opis\Closure\SerializableClosure;
use Opis\Routing\FilterCollection;

class ViewApp implements Serializable
{
    /** @var array */
    protected $cache;

    /** @var Router*/
    protected $router;

    /** @var  EngineResolver */
    protected $resolver;

    /** @var RouteCollection*/
    protected $collection;

    /** @var  FilterCollection */
    protected $filters;

    /** @var    mixed|null  */
    protected $param;

    /** @var  mixed */
    protected $viewItem;

    /**
     * ViewRouter constructor.
     * @param RouteCollection|null $collection
     * @param EngineResolver|null $resolver
     */
    public function __construct(RouteCollection $collection = null, EngineResolver $resolver = null)
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
        $this->viewItem = null;
    }

    /**
     * Get filters
     * 
     * @return  FilterCollection
     */
    protected function getFilters(): FilterCollection
    {
        if ($this->filters === null) {
            $this->filters = new FilterCollection();
            $this->filters->addFilter(new UserFilter());
        }
        return $this->filters;
    }

    /**
     * Get router
     * 
     * @return  Router
     */
    protected function getRouter(): Router
    {
        if ($this->router === null) {
            $this->router = new Router($this->collection, null, $this->getFilters());
        }

        return $this->router;
    }

    /**
     * Get the collection of routes
     * 
     * @return RouteCollection
     */
    public function getRouteCollection(): RouteCollection
    {
        return $this->collection;
    }

    /**
     * Get the engine resolver instance
     * 
     * @return  EngineResolver
     */
    public function getEngineResolver(): EngineResolver
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
     * @return Route
     */
    public function handle(string $pattern, callable $resolver, int $priority = 0): Route
    {
        $route = new Route($pattern, $resolver);
        $route->set('priority', $priority);
        $this->collection->addRoute($route);
        $this->cache = array(); //clear cache
        return $route;
    }

    /**
     * Render a view
     * 
     * @param   ViewableInterface|mixed  $view
     * 
     * @return  string
     */
    public function render($view): string
    {
        if (!($view instanceof ViewableInterface)) {
            return $view;
        }

        $path = $this->resolveViewName($view->viewName());

        if ($path === null) {
            return '';
        }

        $engine = $this->resolver->resolve($path, $this->param);

        $arguments = $view->viewArguments();
        $arguments += $engine->defaultValues($this->viewItem);

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
    public function renderView(string $name, array $arguments = array())
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
    public function resolveViewName(string $name): string
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
        $object = unserialize($data);

        $this->resolver = $object['resolver'];
        $this->collection = $object['collection'];
        $this->insertKey = $object['insertKey'];
        $this->viewKey = $object['viewKey'];
    }
}
