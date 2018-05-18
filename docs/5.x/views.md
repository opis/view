---
layout: project
version: 5.x
title: Views
---
# Views

1. [Introduction](#introduction)
2. [View name](#view-name)
3. [View variables](#view-variables)

## Introduction

A *view* is nothing more but an instance of a class that implements the `Opis\View\IView` interface.
The interface has two methods: `viewName` and `viewVariables`. The first method must return the
name of the view, while the other one must return a mapped array of values, representing the view's variables.

The default implementation of a *view* is provided by the library itself, through its `Opis\View\View` class. 
The constructor of this class takes as arguments the view name and, optionally, an array of variables.

```php
use Opis\View\View;

$foo = new View('foo');

$bar = new View('bar', [
    'var' => 'value'
]);
```

## View name

The view name is an opaque identifier for a view instance. This means that it has 
absolutely no connection with the name of the template file, nor with its location.
It's not the view's job to know where to get its template from.

```php
$view = new View('foo');

// Get the view's name
echo $view->viewName(); //> foo
```

The view name's value can be split into segments by using the `.` dot symbol.

```php
$view = new View('user.profile');
``` 

Splitting a view name into segments is useful, not only when it comes to resolve them
to a location, but also to avoid naming conflicts between different vendors.

```php
$view1 = new View('opis.user.profile');
$view2 = new View('vendor.user.profile');
$view3 = new View('acme.blog.article');
``` 

## View variables

Variables are represented by a key-value mapped array, where the key represents
the variable's name and the value represent's the variable's value.

```php
$view = new View('test', [
    'foo' => 'Foo',
    'bar' => 'Bar'
]);

print_r($view->viewVariables());

/*
Array
(
    [foo] => Foo
    [bar] => Bar
)
*/
```

The variables names from a template are replaced with the values provided by the view instance

**View:**

```php
$view = new View('article', [
    'title' => 'Hello, World!',
    'content' => 'This is the content of my article'
]);
```

**Template:**

```php
<div class="article">
    <h1><?= $title ?></h1>
    <div><?= $content ?></div>
</div>
```

**Result:**

```html
<div class="article">
    <h1>Hello, World!</h1>
    <div>This is the content of my article</div>
</div>
```
