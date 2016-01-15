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
use Opis\Closure\SerializableClosure;

class EngineResolver implements Serializable
{
    /** @var    array */
    protected $engines = array();

    /** @var    \Opis\View\EngineInterface */
    protected $defaultEngine;

    /**
     * Register a new engine
     * 
     * @param   Closure $builder
     * @param   int     $priority
     * 
     * @return  \Opis\View\EngineEntry
     */
    public function register(Closure $builder, $priority = 0)
    {
        static $eq;

        if ($eq === null) {
            $arr = array(array(0, -1), array(0, 1));
            uasort($arr, function($a, $b) {
                return 0;
            });
            $arr = reset($arr);
            $eq = $arr[1];
        }

        $entry = new EngineEntry($builder);

        $this->engines[] = array(
            'engine' => $entry,
            'priority' => $priority,
        );

        uasort($this->engines, function($a, $b) use($eq){
            
            if ($a['priority'] === $b['priority']) {
                return $eq;
            }
            
            return $a['priority'] < $b['priority'] ? 1 : -1;
        });

        return $entry;
    }

    /**
     * Resolve a path to a render engine
     * 
     * @param   string  $path
     * 
     * @return  \Opis\View\EngineInterface
     */
    public function resolve($path)
    {
        foreach ($this->engines as &$entry) {
            $engine = $entry['engine'];

            if ($engine->canHandle($path)) {
                return $engine->instance();
            }
        }
        return $this->getDefaultEngine();
    }

    /**
     * Get the default render engine
     * 
     * @return \Opis\View\EngineInterface
     */
    protected function getDefaultEngine()
    {
        if ($this->defaultEngine === null) {
            $this->defaultEngine = new PHPEngine();
        }
        return $this->defaultEngine;
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
     * @param   string  $data
     */
    public function unserialize($data)
    {
        $this->engines = unserialize($data);
    }
}

class EngineEntry implements Serializable
{
    /** @var    Closure */
    protected $builder;

    /** @var    Closure */
    protected $handler;

    /** @var    \Opis\View\EngineInterface */
    protected $instance;

    /**
     * Constructor
     * 
     * @param   Closure $builder
     */
    public function __construct(Closure $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Add a handler callback
     * 
     * @param   Closure $handler
     */
    public function handle(Closure $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Check if the path can be handled
     * 
     * @param   string  $path
     * 
     * @return  boolean
     */
    public function canHandle($path)
    {
        $handler = $this->handler;
        return $handler($path);
    }

    /**
     * Get an instance of an engine
     * 
     * @return  \Opis\View\EngineInterface
     */
    public function instance()
    {
        if ($this->instance === null) {
            $builder = $this->builder;
            $this->instance = $builder();
        }

        return $this->instance;
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
            'handler' => SerializableClosure::from($this->handler),
            'builder' => SerializableClosure::from($this->builder),
        ));
        SerializableClosure::exitContext();
        return $object;
    }

    /**
     * Unserialize
     * 
     * @param   string  $data
     */
    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);
        $this->handler = $object['handler']->getClosure();
        $this->builder = $object['builder']->getClosure();
    }
}
