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

use Opis\Utils\SortableList;

class EngineResolver extends SortableList
{
    private Renderer $renderer;

    /** @var Engine[]|null */
    private ?array $cache = null;

    /**
     * Constructor
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        parent::__construct([], true, true);
        $this->renderer = $renderer;
    }

    /**
     * Register a new view engine
     *
     * @param callable $factory
     * @param int $priority
     * @return EngineResolver
     */
    public function register(callable $factory, int $priority = 0): self
    {
        $this->cache = null;
        $this->addItem($factory, $priority);
        return $this;
    }

    /**
     * Resolve a path to a render engine
     *
     * @param   string $path
     *
     * @return  Engine
     */
    public function resolve(string $path): Engine
    {
        if ($this->cache === null) {
            $this->cache = [];
            foreach ($this->getValues() as $factory) {
                $instance = $factory($this->renderer);
                if ($instance instanceof Engine) {
                    $this->cache[] = $instance;
                }
            }
        }

        foreach ($this->cache as $engine) {
            if ($engine->canHandle($path)) {
                return $engine;
            }
        }

        return $this->renderer->getDefaultEngine();
    }

    public function __serialize(): array
    {
        return [
            'renderer' => $this->renderer,
            'parent' => parent::__serialize(),
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->renderer = $data['renderer'];
        parent::__unserialize($data['parent']);
    }
}
