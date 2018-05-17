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

namespace Opis\View\Test;

use Opis\View\IEngine;

class ViewEngine2 implements IEngine
{
    public function defaultValues($viewItem): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function build(string $path, array $vars = array()): string
    {
        return strtoupper($path) . '!';
    }

    /**
     * @inheritDoc
     */
    public function canHandle(string $path): bool
    {
        return true;
    }
}