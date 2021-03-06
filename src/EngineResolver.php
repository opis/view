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
use Opis\Closure\SerializableClosure;

class EngineResolver implements Serializable
{
    /** @var array */
    protected $engines = [];

    /** @var  ViewRenderer */
    protected $renderer;

    /** @var IEngine[]|null */
    protected $cache;

    /**
     * Constructor
     * @param ViewRenderer $renderer
     */
    public function __construct(ViewRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Register a new view engine
     *
     * @param callable $factory
     * @param int $priority
     * @return EngineResolver
     */
    public function register(callable $factory, $priority = 0): self
    {
        array_unshift($this->engines, [$factory, $priority]);
        $this->cache = null;

        $sorted = false;
        while (!$sorted) {
            $sorted = true;
            for ($i = 0, $l = count($this->engines) - 1; $i < $l; $i++) {
                if ($this->engines[$i][1] < $this->engines[$i + 1][1]) {
                    $tmp = $this->engines[$i];
                    $this->engines[$i] = $this->engines[$i + 1];
                    $this->engines[$i + 1] = $tmp;
                    $sorted = false;
                }
            }
        }

        return $this;
    }

    /**
     * Resolve a path to a render engine
     *
     * @param   string $path
     *
     * @return  IEngine
     */
    public function resolve(string $path): IEngine
    {
        if ($this->cache === null) {
            $this->cache = [];
            foreach ($this->engines as $engine) {
                $this->cache[] = $engine[0]($this->renderer);
            }
        }

        foreach ($this->cache as $engine) {
            if ($engine->canHandle($path)) {
                return $engine;
            }
        }

        return $this->renderer->getDefaultEngine();
    }


    /**
     * Serialize
     *
     * @return  string
     */
    public function serialize()
    {
        SerializableClosure::enterContext();

        $engines = $this->engines;

        foreach ($engines as &$engine) {
            if ($engine[0] instanceof \Closure) {
                $engine[0] = SerializableClosure::from($engine[0]);
            }
        }

        $object = serialize([
            'engines' => $engines,
            'renderer' => $this->renderer
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

        foreach ($object['engines'] as &$engine) {
            if ($engine[0] instanceof SerializableClosure) {
                $engine[0] = $engine[0]->getClosure();
            }
        }

        $this->engines = $object['engines'];
        $this->renderer = $object['renderer'];
    }
}
