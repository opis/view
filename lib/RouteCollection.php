<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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

use Opis\Routing\Collections\RouteCollection as BaseCollection;

class RouteCollection extends BaseCollection
{
    
    protected $dirty = false;
    
    public function sort()
    {
        if($this->dirty)
        {
            uasort($this->collection, function(&$a, &$b){
                $v1 = $a->getPriority();
                $v2 = $b->getPriority();
                if($v1 === $v2)
                {
                    return 0;
                }
                return $v1 < $v2 ? 1 : -1;
            });
            
            $this->dirty = false;
        }
    }
    
    public function offsetSet($offset, $value)
    {
        $this->dirty = true;
        parent::offsetSet($offset, $value);
    }
    
    protected function checkType($value)
    {
        if(!($value instanceof Route))
        {
            throw new InvalidArgumentException('Expected \Opis\View\Route');
        }
    }
}