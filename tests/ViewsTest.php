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

use Opis\View\ViewApp;
use Opis\View\IEngine;

class ViewsTest extends \PHPUnit\Framework\TestCase
{
    /** @var    \Opis\View\ViewApp */
    protected $view;

    public function setUp()
    {
        $this->view = new ViewApp();
    }

    public function testResolve()
    {
        $this->view->handle('foo', function () {
            return 'bar';
        });

        $this->assertEquals('bar', $this->view->resolveViewName('foo'));
    }

    public function testResolveMissing()
    {
        $this->assertEquals(null, $this->view->resolveViewName('missing'));
    }

    public function testResolveMultiple()
    {
        $this->view->handle('foo', function () {
            return 'bar';
        });

        $this->view->handle('foo', function () {
            return 'baz';
        });

        $this->view->handle('foo', function () {
            return 'qux';
        });

        $this->assertEquals('qux', $this->view->resolveViewName('foo'));
    }

    public function testResolvePriority()
    {
        $this->view->handle('foo', function () {
            return 'bar';
        }, 1);

        $this->view->handle('foo', function () {
            return 'baz';
        });

        $this->assertEquals('bar', $this->view->resolveViewName('foo'));
    }

    public function testEngine()
    {
        $this->view->getEngineResolver()->register(function () {
            return new ViewEngine1();
        })->handle(function ($path) {
            return true;
        });

        $this->view->handle('foo', function () {
            return 'bar';
        });

        $this->assertEquals('BAR', $this->view->renderView('foo'));
    }

    public function testEnginePriority1()
    {
        $this->view->getEngineResolver()->register(function () {
            return new ViewEngine1();
        })->handle(function ($path) {
            return true;
        });

        $this->view->getEngineResolver()->register(function () {
            return new ViewEngine2();
        })->handle(function ($path) {
            return true;
        });

        $this->view->handle('foo', function () {
            return 'bar';
        });

        $this->assertEquals('BAR!', $this->view->renderView('foo'));
    }

    public function testEnginePriority2()
    {
        $this->view->getEngineResolver()->register(function () {
            return new ViewEngine1();
        }, 1)->handle(function ($path) {
            return true;
        });

        $this->view->getEngineResolver()->register(function () {
            return new ViewEngine2();
        })->handle(function ($path) {
            return true;
        });

        $this->view->handle('foo', function () {
            return 'bar';
        });

        $this->assertEquals('BAR', $this->view->renderView('foo'));
    }

    public function testRenderMethod1()
    {
        $this->assertEquals('foo', $this->view->render('foo'));
    }
}
