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

use Opis\Routing\Path;
use Opis\Routing\Router;
use Opis\Routing\Callback;
use Opis\Routing\FilterInterface;
use Opis\Routing\Route as BaseRoute;

class UserFilter implements FilterInterface
{

    /**
     * Check if a route pass this filter
     * 
     * @param   \Opis\Routing\Router    $router
     * @param   \Opis\Routing\Path      $path
     * @param   \Opis\Routing\Route     $route
     * 
     * @return  boolean
     */
    public function pass(Router $router, Path $path, BaseRoute $route)
    {
        $filter = $route->get('filter');

        if (!is_callable($filter)) {
            return true;
        }

        $callback = new Callback($filter);
        
        $values = $route->compile()->bind($path);
        $specials = $router->getSpecialValues();

        $arguments = array();

        $parameters = $callback->getParameters();

        foreach ($parameters as $param) {
            $name = $param->getName();

            if (isset($values[$name])) {
                $arguments[] = $values[$name];
            }
            elseif(isset($specials[$name])){
                $arguments[] = $specials[$name];
            }
            elseif ($param->isOptional()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                $arguments[] = null;
            }
        }

        return $callback->invoke($arguments);
    }
}
