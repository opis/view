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
use Opis\View\ViewApp;
use Opis\View\EngineInterface;

class ViewsTest extends PHPUnit_Framework_TestCase
{
    /** @var    \Opis\View\ViewApp */
    protected $view;

    public function setUp()
    {
        $this->view = new ViewApp();
    }

    public function testResolve()
    {
        $this->view->handle('foo', function() {
            return 'bar';
        });

        $this->assertEquals('bar', $this->view->resolveViewName('foo'));
    }

    public function testResolveMultiple()
    {
        $this->view->handle('foo', function() {
            return 'bar';
        });

        $this->view->handle('foo', function() {
            return 'baz';
        });

        $this->assertEquals('baz', $this->view->resolveViewName('foo'));
    }

    public function testResolvePriority()
    {
        $this->view->handle('foo', function() {
            return 'bar';
        }, 1);

        $this->view->handle('foo', function() {
            return 'baz';
        });

        $this->assertEquals('bar', $this->view->resolveViewName('foo'));
    }

    public function testEngine()
    {
        $this->view->getEngineResolver()->register(function() {
            return new Engine1();
        })->handle(function($path) {
            return true;
        });

        $this->view->handle('foo', function() {
            return 'bar';
        });

        $this->assertEquals('BAR', $this->view->renderView('foo'));
    }

    public function testEnginePriority1()
    {
        $this->view->getEngineResolver()->register(function() {
            return new Engine1();
        })->handle(function($path) {
            return true;
        });

        $this->view->getEngineResolver()->register(function() {
            return new Engine2();
        })->handle(function($path) {
            return true;
        });

        $this->view->handle('foo', function() {
            return 'bar';
        });

        $this->assertEquals('BAR!', $this->view->renderView('foo'));
    }

    public function testEnginePriority2()
    {
        $this->view->getEngineResolver()->register(function() {
            return new Engine1();
        }, 1)->handle(function($path) {
            return true;
        });

        $this->view->getEngineResolver()->register(function() {
            return new Engine2();
        })->handle(function($path) {
            return true;
        });

        $this->view->handle('foo', function() {
            return 'bar';
        });

        $this->assertEquals('BAR', $this->view->renderView('foo'));
    }
    
    public function testRenderMethod1()
    {
        $this->assertEquals('foo', $this->view->render('foo'));
    }
}

class Engine1 implements EngineInterface
{
    public function defaultValues($viewItem): array
    {
        return [];
    }

    public function build(string $path, array $data = array()): string
    {
        return strtoupper($path);
    }
}

class Engine2 implements EngineInterface
{
    public function defaultValues($viewItem): array
    {
        return [];
    }

    public function build(string $path, array $data = array()): string
    {
        return strtoupper($path) . '!';
    }
}
