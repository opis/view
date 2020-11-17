<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

use Opis\Utils\{ArgumentResolver, RegexBuilder, SortableList};

class Renderer extends SortableList
{
    private array $cache = [];
    private EngineResolver $resolver;
    private Engine $defaultEngine;
    private ?RegexBuilder $regexBuilder = null;

    public function __construct(?Engine $engine = null)
    {
        $this->resolver = new EngineResolver($this);
        $this->defaultEngine = $engine ?? new PHPEngine();
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
     * @return Engine
     */
    public function getDefaultEngine(): Engine
    {
        return $this->defaultEngine;
    }

    /**
     * @return RegexBuilder
     */
    public function getRegexBuilder(): RegexBuilder
    {
        if ($this->regexBuilder === null) {
            $this->regexBuilder = new RegexBuilder([
                RegexBuilder::SEPARATOR_SYMBOL => '.',
                RegexBuilder::CAPTURE_MODE => RegexBuilder::CAPTURE_LEFT,
            ]);
        }

        return $this->regexBuilder;
    }

    /**
     * Register a new view
     *
     * @param   string $pattern
     * @param   callable $resolver
     * @param   int $priority
     *
     * @return ViewHandler
     */
    public function handle(string $pattern, callable $resolver, int $priority = 0): ViewHandler
    {
        $this->cache = [];
        $handler = new DefaultViewHandler($this, $pattern, $resolver);
        $this->addItem($handler, $priority);
        return $handler;
    }

    /**
     * Render a view
     * @param View|string $view
     * @return string
     */
    public function render($view): string
    {
        if (!($view instanceof View)) {
            return $view;
        }

        $path = $this->resolveViewName($view->getViewName());

        if ($path === null) {
            return '';
        }

        $engine = $this->resolver->resolve($path);

        return $engine->build($path, $view->getViewVariables());
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
        return $this->render(new DefaultView($name, $vars));
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
            $this->cache[$name] = $this->find($name);
        }
        return $this->cache[$name];
    }

    public function __serialize(): array
    {
        return [
            'resolver' => $this->resolver,
            'defaultEngine' => $this->defaultEngine,
            'parent' => parent::__serialize(),
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->resolver = $data['resolver'];
        $this->defaultEngine = $data['defaultEngine'];
        parent::__unserialize($data['parent']);
    }

    private function find(string $name): ?string
    {
        /** @var DefaultViewHandler $handler */
        foreach ($this->getValues() as $handler) {
            if (preg_match($handler->getRegex(), $name)) {
                $resolver = new ArgumentResolver();
                $resolver->addValues($this->getRegexBuilder()->getValues($handler->getRegex(), $name));
                if (null !== $filter = $handler->getFilter()) {
                    $arguments = $resolver->resolve($filter);
                    if (!(bool)$filter(...$arguments)) {
                        continue;
                    }
                }
                $callback = $handler->getCallback();
                $arguments = $resolver->resolve($callback);
                return $callback(...$arguments);
            }
        }

        return null;
    }
}
