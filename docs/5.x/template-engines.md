---
layout: project
version: 5.x
title: Template engines
---
# Template engines

1. [Overview](#overview)
2. [Registering new engines](#registering-new-engines)

## Overview

Even though the view renderer provides us the `render` method, it's not capable
of actually rendering a view by itself. 
Instead, it delegates this job to a template engine. The template engine is an
instance of a class that implements the `Opis\View\IEngine` interface.
This interface provides 2 methods that need to be implemented: `canHandle` and `build`.

The `canHanle` method takes a single argument - the path to a template - and returns
`true` if it can handle that path, or `false` otherwise. This is how we are able
to use different template engine simultaneously.

The `build` method takes two arguments: the path to a template file and an array of variables.
It will use these arguments to generate the content and return the result.

```php
use Opis\View\IEngine;

class MyEngine implements IEngine
{
    /**
     * @inheritdoc
     */
    public function build(string $path, array $vars): string
    {
        // Build and return the content
    }
    
    /**
     * @inheritdoc
     */
    public function canHandle(string $path): bool
    {
        // Check if the $path can be handled by this engine
    }
}
```

## Registering new engines

**Opis View** provides, by default, a template engine - implemented in the `Opis\View\PHPEngine` class -
that uses PHP itself as a templating language.

If you have created a custom template engine, you must register it first, in order to be able to use it.
Registering a new template engine is done by calling the `register` method.

The method takes as arguments a callback - which will act as 
a factory and return an instance of our engine - and, optionally, a priority (default is `0`). 
The callback factory will receive, as an argument, the view renderer instance where the new engine 
will be registered.

```php
use Opis\View\{
    ViewRenderer
};

$renderer = new ViewRenderer();

$renderer->getEngineResolver()
         ->register(function(ViewRenderer $renderer){
            return new MyCustomEngine($renderer);
         });
``` 

You can override an existing engine by calling the `register` method and using a higher priority.


```php
$renderer->getEngineResolver()
         ->register(function(ViewRenderer $renderer){
            return new MyPHPEngine($renderer);
         }, 1); // priority 1
``` 

