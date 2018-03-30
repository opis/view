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

use Opis\Pattern\RegexBuilder;
use Opis\Routing\RouteCollection as BaseCollection;

/**
 * @method Route createRoute(string $pattern, callable $action, string $name = null)
 */
class RouteCollection extends BaseCollection
{
    /**
     * RouteCollection constructor
     */
    public function __construct()
    {
        parent::__construct(static::class . '::factory', new RegexBuilder([
            RegexBuilder::SEPARATOR_SYMBOL => '.',
            RegexBuilder::CAPTURE_MODE => RegexBuilder::CAPTURE_LEFT,
        ]), 'priority');
    }

    /**
     * @param RouteCollection $collection
     * @param string $id
     * @param string $pattern
     * @param callable $action
     * @param string|null $name
     * @return Route
     */
    protected static function factory(
        RouteCollection $collection,
        string $id,
        string $pattern,
        callable $action,
        string $name = null
    ): Route {
        return new Route($collection, $id, $pattern, $action, $name);
    }
}
