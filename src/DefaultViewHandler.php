<?php
/* ===========================================================================
 * Copyright 2020 Zindex Software
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

use Opis\Utils\RegexBuilder;

class DefaultViewHandler implements ViewHandler
{
    /** @var callable|null */
    private $filter = null;

    /** @var callable */
    private $callback;

    private string $pattern;
    private Renderer $renderer;
    private array $placeholders = [];
    private ?string $regex = null;

    public function __construct(Renderer $renderer, string $pattern, callable $callback)
    {
        $this->renderer = $renderer;
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    public function filter(callable $callback): ViewHandler
    {
        $this->filter = $callback;
        return $this;
    }

    public function where(string $name, string $regex): ViewHandler
    {
        $this->regex = null;
        $this->placeholders[$name] = $regex;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereIn(string $name, array $values): ViewHandler
    {
        if (empty($values)) {
            return $this;
        }

        $delimiter = $this->renderer->getRegexBuilder()->getOptions()[RegexBuilder::REGEX_DELIMITER];

        $value = implode('|', array_map(static fn ($value) => preg_quote($value, $delimiter), $values));

        return $this->where($name, $value);
    }

    public function getRegex(): string
    {
        if ($this->regex === null) {
            $this->regex = $this->renderer->getRegexBuilder()->getRegex($this->pattern, $this->placeholders);
        }

        return $this->regex;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    public function getFilter(): ?callable
    {
        return $this->filter;
    }
}