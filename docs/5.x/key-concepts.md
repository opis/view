---
layout: project
version: 5.x
title: Key concepts
---
# Key concepts

1. [Views](#views)
2. [View renderer](#view-renderer)
3. [Template engines](#template-engines)

## Views

A *view* is nothing more but an instance of a class that implements the `Opis\View\IView` interface.
The interface has two methods: `viewName` and `viewAruments`. The first method must return the
name of the view, while the other one must return an array of values.

The default implementation of a *view* is provided by the library itself, through its `Opis\View\View` class. 
The constructor of this class takes as arguments the view name and, optionally, an array of values.

```php
use Opis\View\View;

$foo = new View('foo');

$bar = new View('bar', [
    'key' => 'value'
]);
```

## View renderer

The central part of the whole rendering system is the *view renderer*. As its name suggests,
the renderer is responsible for rendering *views*. Rendering a *view* is done with the help
of the `render` method, which takes as an argument a *view* instance and returns a string.

```php
use Opis\View\{
    ViewRenderer, View
};

$renderer = new ViewRenderer();

$view = new View('foo', [
    'bar' => 'baz'
]);

echo $renderer->render($view);
```

## Template engines

The *view renderer* is not capable of rendering a view directly.
Instead, it must delegate this task to a template engine, like [Twig].
The template engine is represented by an instance of the `Opis\View\IEngine` interface. 
**Opis View** provides an implicit template engine, that uses PHP as its templating language.

```php
<div><?= $text ?></div>
```

[Twig]: https://twig.symfony.com "Twig"