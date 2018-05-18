---
layout: project
version: 5.x
title: About
lib: 
    name: opis/view
    version: 5.0.0
---
# About

**Opis View** is a rendering system that can be integrated with multiple template
engines and is capable of using those template engines simultaneously.

## License
**Opis View** is licensed under the [Apache License, Version 2.0][apache_license].

## Requirements
* PHP 7.0.0 or higher
* [Opis Routing] ^5.0.0

## Installation

**Opis View** is available on [Packagist] and it can be installed from a 
command line interface by using [Composer]. 

```bash
composer require {{page.lib.name}}
```

Or you could directly reference it into your `composer.json` file as a dependency

```json
{
    "require": {
        "{{page.lib.name}}": "^{{page.lib.version}}"
    }
}
```

[apache_license]: http://www.apache.org/licenses/LICENSE-2.0 "Project license" 
{:rel="nofollow" target="_blank"}
[Packagist]: https://packagist.org/packages/{{page.lib.name}} "Packagist" 
{:rel="nofollow" target="_blank"}
[Composer]: http://getcomposer.org "Composer" 
{:rel="nofollow" target="_blank"}
[Opis Routing]: /routing  "Opis Routing ^5.0.0" 
{:data-toggle="tooltip"}
