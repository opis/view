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
use Opis\Closure\SerializableClosure;

class EngineEntry implements Serializable
{
    /** @var callable */
    protected $factory;

    /** @var  callable */
    protected $handler;

    /** @var  EngineInterface */
    protected $instance;

    /** @var  int */
    protected $priority;

    /**
     * Constructor
     *
     * @param  callable $factory
     * @param int   $priority (optional)
     */
    public function __construct(callable $factory, int $priority = 0)
    {
        $this->factory = $factory;
        $this->priority = $priority;
    }

    /**
     * Add a handler callback
     *
     * @param  callable $handler
     */
    public function handle(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Check if the path can be handled
     *
     * @param   string      $path
     *
     * @return  boolean
     */
    public function canHandle(string $path): bool
    {
        $handler = $this->handler;
        return $handler($path);
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Get an instance of an engine
     *
     * @param ViewApp $viewApp
     *
     * @return EngineInterface
     */
    public function instance(ViewApp $viewApp): EngineInterface
    {
        if ($this->instance === null) {
            $factory = $this->factory;
            return $this->instance = $factory($viewApp);
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
            'handler' => $this->handler instanceof \Closure ? SerializableClosure::from($this->handler) : $this->handler,
            'factory' => $this->factory instanceof \Closure ? SerializableClosure::from($this->factory) : $this->factory
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
        $object = unserialize($data);
        foreach ($object as $key => &$value){
            if($value instanceof SerializableClosure){
                $value = $value->getClosure();
            }
            $this->{$key} = $value;
        }
    }
}