---
layout: project
version: 5.x
title: View renderer
---
# View renderer

1. [Rendering views](#rendering-views)

## Rendering views

The view renderer is the central part of the rendering system, and it's
represented by an instance of the `Opis\View\ViewRenderer` class. 
Let's assume we have the following template for a blog article and we want
to associate it a view named `blog.article`.

**article.php**

```php
<div class="article">
    <h2><?= $title ?></h2>
    <div><?= $content ?></div>
</div>
```

In order to be able to render that view, we must first provide a way to resolve the view's name to the location
of our article template. This is done with the help of the `handle` method.

```php
use Opis\View\{
    ViewRenderer, View
};

$renderer = new ViewRenderer();

$renderer->handle('blog.article', function(){
    return '/path/to/article.php';
});
```

To test that our view name is resolved correctly, we could pass it to the
`resolveViewName` method.

```php
echo $renderer->resolveViewName('blog.article'); //> /path/to/article.php
```

Now we can start rendering article views, by using the `render` method.

```php
$article = new View('blog.article', [
    'title' => 'My first article',
    'content' => 'This is my first article'
]);

echo $renderer->render($article);
```

You can use the `renderView` method as a shortcut for the above example.

```php
echo $renderer->renderView('blog.article', [
    'title' => 'My first article',
    'content' => 'This is my first article'
]);
```

**Result:**

```html
<div class="article">
    <h2>My first article</h2>
    <div>This is my first article</div>
</div>
```


