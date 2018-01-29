<?php
/* ===========================================================================
 * Copyright 2013-2017 The Opis Project
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

use Serializable;
use Opis\Routing\Context;
use Opis\Routing\Dispatcher;
use Opis\Routing\IDispatcher;
use Opis\Routing\Router;
use Opis\Closure\SerializableClosure;
use Opis\Routing\FilterCollection;

class ViewApp implements Serializable
{
    /** @var array */
    protected $cache;

    /** @var Router*/
    protected $router;

    /** @var Dispatcher */
    protected $dispatcher;

    /** @var EngineResolver */
    protected $resolver;

    /** @var RouteCollection*/
    protected $collection;

    /** @var FilterCollection */
    protected $filters;

    /** @var EngineInterface */
    protected $defaultEngine;

    /**
     * ViewApp constructor.
     * @param RouteCollection|null $collection
     * @param EngineResolver|null $resolver
     * @param EngineInterface|null $engine
     */
    public function __construct(RouteCollection $collection = null, EngineResolver $resolver = null, EngineInterface $engine = null)
    {
        if ($collection === null) {
            $collection = new RouteCollection();
        }

        if ($resolver === null) {
            $resolver = new EngineResolver();
        }

        if($engine === null){
            $engine = new PHPEngine();
        }

        $resolver->setViewApp($this);

        $this->cache = [];
        $this->collection = $collection;
        $this->resolver = $resolver;
        $this->defaultEngine = $engine;
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
            $this->router = new Router($this->collection, $this->getDispatcher(), $this->getFilters());
        }

        return $this->router;
    }

    /**
     * Get the dispatcher
     *
     * @return IDispatcher
     */
    protected function getDispatcher(): IDispatcher
    {
        if($this->dispatcher === null){
            $this->dispatcher = new Dispatcher();
        }

        return $this->dispatcher;
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
     * Get the default render engine
     *
     * @return EngineInterface
     */
    public function getDefaultEngine(): EngineInterface
    {
        return $this->defaultEngine;
    }

    /**
     * Register a new view
     *
     * @param   string $pattern
     * @param   callable $resolver
     * @param   int $priority
     *
     * @return Route
     * @throws \Exception
     */
    public function handle(string $pattern, callable $resolver, int $priority = 0): Route
    {
        $route = new Route($pattern, $resolver);
        $route->set('priority', $priority);
        $this->collection->addRoute($route);
        $this->cache = []; //clear cache
        return $route;
    }

    /**
     * Render a view
     *
     * @param   IView|mixed $view
     *
     * @return  string
     * @throws \Exception
     */
    public function render($view): string
    {
        if (!($view instanceof IView)) {
            return $view;
        }

        $path = $this->resolveViewName($view->viewName());

        if ($path === null) {
            return '';
        }

        $engine = $this->resolver->resolve($path);

        return $engine->build($path, $view->viewArguments());
    }

    /**
     * Render a view
     *
     * @param   string $name
     * @param   array $arguments
     *
     * @return  mixed
     * @throws \Exception
     */
    public function renderView(string $name, array $arguments = array())
    {
        return $this->render(new View($name, $arguments));
    }

    /**
     * Resolve a view's name
     *
     * @param   string $name
     *
     * @return  string
     * @throws \Exception
     */
    public function resolveViewName(string $name): string
    {
        if (!isset($this->cache[$name])) {
            $this->collection->sort();
            $this->cache[$name] = $this->getRouter()->route(new Context($name));
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
    }
}
