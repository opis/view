<?php
/* ===========================================================================
 * Copyright 2013-2018 The Opis Project
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

class EngineResolver implements Serializable
{
    /** @var EngineEntry[] */
    protected $engines = [];

    /** @var  ViewRenderer */
    protected $renderer;

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
     * @return EngineEntry
     */
    public function register(callable $factory, $priority = 0): EngineEntry
    {
        $entry = new EngineEntry($factory, $priority);
        $this->engines[] = $entry;

        uasort($this->engines, function (EngineEntry $a, EngineEntry $b) {
            return $a->getPriority() <= $b->getPriority() ? 1 : -1;
        });

        return $entry;
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
        foreach ($this->engines as $engine) {
            if ($engine->canHandle($path)) {
                return $engine->instance($this->renderer);
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
        return serialize($this->engines);
    }

    /**
     * Unserialize
     *
     * @param   string $data
     */
    public function unserialize($data)
    {
        $this->engines = unserialize($data);
    }
}
