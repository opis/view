Opis View
=========
[![Latest Stable Version](https://poser.pugx.org/opis/view/version.png)](https://packagist.org/packages/opis/view)
[![Latest Unstable Version](https://poser.pugx.org/opis/view/v/unstable.png)](//packagist.org/packages/opis/view)
[![License](https://poser.pugx.org/opis/view/license.png)](https://packagist.org/packages/opis/view)

A **must have** view component that can be integrated with multiple rendering engines simultaneously.

###Installation

This library is available on [Packagist](https://packagist.org/packages/opis/view) and can be installed using [Composer](http://getcomposer.org)

```json
{
    "require": {
        "opis/view": "2.4.*"
    }
}
```

###Documentation

###Examples

```php
use Opis\View\ViewRouter;
use Opis\View\View;

$router = new ViewRouter();

$router->handle('view.{name}', function($name){
    return  '/some/path/' . $name . '.php';
})
->where('name', 'welcome|account');

print $router->renderView('view.welcome');
//Or..
print $router->render(new View('view.welcome'));

//Serialize and unserialize

$router = unserialize(serialize($router));

print $router->renderView('view.account', array('user' => 'Opis'));
//Or..
print $router->render(new View('view.account', array('user' => 'Opis')));
```