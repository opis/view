<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

use Throwable;

class PHPEngine implements Engine
{
    /**
     * Build
     *
     * @param   string $path
     * @param   array $vars
     *
     * @return  string
     * @throws Throwable
     */
    public function build(string $path, array $vars = []): string
    {
        ${'#path'} = $path;
        ${'#vars'} = $vars;

        unset($path, $vars);

        ob_start();

        extract(${'#vars'});

        try {
            /** @noinspection PhpIncludeInspection */
            include ${'#path'};
        } catch (Throwable $e) {
            ob_get_clean();
            throw $e;
        }

        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    public function canHandle(string $path): bool
    {
        return true;
    }
}
