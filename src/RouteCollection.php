<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

use Opis\Closure\SerializableClosure;
use Opis\Routing\Compiler;
use Opis\Routing\Route;
use Opis\Routing\RouteCollection as BaseCollection;

class RouteCollection extends BaseCollection
{
    /** @var    boolean */
    protected $dirty = false;

    /**
     * RouteCollection constructor
     */
    public function __construct()
    {
        parent::__construct(new Compiler([
            Compiler::TAG_SEPARATOR => '.',
            Compiler::CAPTURE_MODE => (Compiler::CAPTURE_LEFT | Compiler::CAPTURE_TRAIL),
        ]));
    }

    /**
     * Sort event handlers
     */
    public function sort()
    {
        if (!$this->dirty) {
            return;
        }

        $this->regex = null;
        $this->dirty = false;

        $length = count($this->routes) - 1;

        if($length <= 0){
            return;
        }

        /** @var string[] $keys */
        $keys = array_reverse(array_keys($this->routes));
        /** @var Route[] $values */
        $values = array_reverse(array_values($this->routes));

        $done = false;

        while (!$done){
            $done = true;
            for ($i = 0; $i < $length; $i++){
                if(($values[$i]->get('priority') <=> $values[$i + 1]->get('priority')) < 0){
                    $vtmp = $values[$i + 1];
                    $ktmp = $keys[$i + 1];
                    $values[$i + 1] = $values[$i];
                    $keys[$i + 1] = $keys[$i];
                    $values[$i] = $vtmp;
                    $keys[$i] = $ktmp;
                    $done = false;
                }
                echo $i;
            }
        }

        $this->routes = array_combine($keys, $values);
    }

    /**
     * @param Route $route
     * @return BaseCollection
     */
    public function addRoute(Route $route): parent
    {
        $this->dirty = true;
        return parent::addRoute($route);
    }

    /**
     * Serialize
     * @return  string
     */
    public function serialize()
    {
        SerializableClosure::enterContext();

        $this->sort();

        $object = serialize(array(
            'dirty' => $this->dirty,
            'parent' => parent::serialize(),
        ));

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
        $this->dirty = $object['dirty'];
        parent::unserialize($object['parent']);
    }
}
