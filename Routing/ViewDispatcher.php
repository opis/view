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

namespace Opis\View\Routing;

use Opis\Routing\Route;
use Opis\Routing\Router;
use Opis\Routing\DispatcherInterface;
use Opis\View\Engines\EngineResolver;

class ViewDispatcher implements DispatcherInterface
{
    
    public function dispatch(Router $router, Route $route)
    {
        $expression = $route->get('expression');
        $values = $expression->extract($router->getView(), $route->getDefaults());
        return call_user_func_array($route->getAction(), $values);
    }
    
}