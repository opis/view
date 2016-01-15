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
    
    protected $engines = array();
    
    protected $defaultEngine;
    
    public function register(Closure $builder, $priority = 0)
    {
        $entry = new EngineEntry($builder);
        
        $this->engines[] = array(
            'engine' => $entry,
            'priority' => $priority,
        );
        
        uasort($this->engines, function(&$a, &$b){
            
            if($a['priority'] === $b['priority'])
            {
                return 0;
            }
            
            return $a['priority'] < $b['priority'] ? 1 : -1;
        });
        
        return $entry;
    }
    
    public function resolve($path)
    {
        foreach($this->engines as &$entry)
        {
            $engine = $entry['engine'];
            
            if($engine->canHandle($path))
            {
                return $engine->instance();
            }
        }
        return $this->getDefaultEngine();
    }
    
    protected function getDefaultEngine()
    {
        if($this->defaultEngine === null)
        {
            $this->defaultEngine = new PHPEngine();
        }
        return $this->defaultEngine;
    }
    
    public function serialize()
    {
        return serialize($this->engines);
    }
    
    public function unserialize($data)
    {
        $this->engines = unserialize($data);
    }
    
}

class EngineEntry implements Serializable
{
    protected $builder;
    
    protected $handler;
    
    protected $instance;
    
    public function __construct(Closure $builder)
    {
        $this->builder = $builder;
    }
    
    public function handle(Closure $handler)
    {
        $this->handler = $handler;
    }
    
    public function canHandle($path)
    {
        $handler = $this->handler;
        return $handler($path);
    }
    
    public function instance()
    {
        if($this->instance === null)
        {
            $builder = $this->builder;
            $this->instance = $builder();   
        }
        
        return $this->instance;
    }
    
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
    
    public function unserialize($data)
    {
        $object = SerializableClosure::unserializeData($data);
        $this->handler = $object['handler']->getClosure();
        $this->builder = $object['builder']->getClosure();
    }
}
