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

use Exception;

class PHPEngine implements IEngine
{
    /** @var    string */
    protected $path;

    /** @var    array */
    protected $data;

    /**
     * Build
     *
     * @param   string $path
     * @param   array $data
     *
     * @return  string
     * @throws Exception
     */
    public function build(string $path, array $data = []): string
    {
        $this->path = $path;
        $this->data = $data;

        unset($path, $data);

        ob_start();

        extract($this->data);

        try {
            /** @noinspection PhpIncludeInspection */
            include $this->path;
        } catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }

        $this->path = $this->data = null;

        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    public function canHandle(string $path): bool
    {
        return preg_match('/.*\.php$/', $path);
    }
}
