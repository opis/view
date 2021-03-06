<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

class ViewRenderer implements Serializable
{
    /** @var array */
    protected $cache;

    /** @var Router */
    protected $router;

    /** @var Dispatcher */
    protected $dispatcher;

    /** @var EngineResolver */
    protected $resolver;

    /** @var RouteCollection */
    protected $collection;

    /** @var FilterCollection */
    protected $filters;

    /** @var IEngine */
    protected $defaultEngine;

    /**
     * ViewApp constructor.
     * @param RouteCollection|null $collection
     * @param IEngine|null $engine
     */
    public function __construct(
        RouteCollection $collection = null,
        IEngine $engine = null
    )
    {
        if ($collection === null) {
            $collection = new RouteCollection();
        }

        if ($engine === null) {
            $engine = new PHPEngine();
        }

        $this->cache = [];
        $this->resolver = new EngineResolver($this);
        $this->collection = $collection;
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
        if ($this->dispatcher === null) {
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
     * @return IEngine
     */
    public function getDefaultEngine(): IEngine
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
     */
    public function handle(string $pattern, callable $resolver, int $priority = 0): Route
    {
        $this->cache = [];
        return $this->collection->createRoute($pattern, $resolver)->set('priority', $priority);
    }

    /**
     * Render a view
     *
     * @param   IView|string|mixed $view
     *
     * @return  string
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

        return $engine->build($path, $view->viewVariables());
    }

    /**
     * Render a view
     *
     * @param   string $name
     * @param   array $vars
     *
     * @return  mixed
     */
    public function renderView(string $name, array $vars = [])
    {
        return $this->render(new View($name, $vars));
    }

    /**
     * Resolve a view's name
     *
     * @param   string $name
     *
     * @return  string|null
     */
    public function resolveViewName(string $name): ?string
    {
        if (!array_key_exists($name, $this->cache)) {
            $this->collection->sort();
            $view = $this->getRouter()->route(new Context($name));
            if (!is_string($view)) {
                $view = null;
            }
            $this->cache[$name] = $view;
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
        $object = serialize([
            'resolver' => $this->resolver,
            'collection' => $this->collection,
        ]);
        SerializableClosure::exitContext();

        return $object;
    }

    /**
     * Unserialize
     *
     * @param   string $data
     */
    public function unserialize($data)
    {
        $object = unserialize($data);

        $this->resolver = $object['resolver'];
        $this->collection = $object['collection'];
    }
}
