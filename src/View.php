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

class View implements IView
{
    /** @var    string */
    protected $name;

    /** @var    array */
    protected $arguments;

    /**
     * Constructor
     * 
     * @param   string  $name
     * @param   array   $arguments  (optional)
     */
    public function __construct(string $name, array $arguments = array())
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    /**
     * Return view's name
     * 
     * @return  string
     */
    public function viewName(): string
    {
        return $this->name;
    }

    /**
     * Return view's arguments
     * 
     * @return  array
     */
    public function viewArguments(): array
    {
        return $this->arguments;
    }
}
