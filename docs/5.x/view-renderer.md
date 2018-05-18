---
layout: project
version: 5.x
title: View renderer
---
# View renderer

1. [Rendering views](#rendering-views)
2. [Resolving view names](#resolving-view-names)
3. [Overriding views](#overriding-views)

## Rendering views

The view renderer is the central part of the rendering system and it's
represented by an instance of the `Opis\View\ViewRenderer` class. 
Let's assume we have the following template for a blog article and we want
to associate it with a view instance named `blog.article`.

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

Now, we can start rendering article views, by using the `render` method.

```php
$article = new View('blog.article', [
    'title' => 'My first article',
    'content' => 'This is my first article'
]);

echo $renderer->render($article);
```

The `renderView` method can be used as a shortcut for the above example.

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

## Resolving view names

Having to resolve each view name individually is an overkill task when
dealing with a considerable amount of names. The solution to this
problem is to split the view name into segments, and make some of those
segments available for being referenced in your callback function.

```php
$renderer->handle('blog.{viewName}' function($viewName) {
    return '/path/to/' . $viewName . '.php';
});
``` 

Now we can render multiple views, named differently, and resolve them only in as single place.

```php
$renderer->renderView('blog.article', [
    'title' => 'Some title',
    'content' => 'Some content'
]);

$render->renderView('blog.comment', [
    'date' => 'Now',
    'comment' => 'Hello, World!'
]);
```

You could also add a regex constraint - with the help of the `where` and `whereIn` methods - 
to filter which view names you are trying to resolve.

```php
// Handle blog.article and blog.comment
$renderer->handle('blog.{viewName}' function($viewName) {
    return '/path/to/' . $viewName . '.php';
})
->whereIn('viewName', ['article', 'comment']);

// Handle blog.list and blog.user
$renderer->handle('blog.{viewName}' function($viewName) {
    return '/other/path/to/' . $viewName . '.php';
})
->whereIn('viewName', ['list', 'user']);

// Handle blog.1, blog.24, blog.2018 etc.
$renderer->handle('blog.{viewName}' function($viewName) {
    return '/some/path/to/blog' . $viewName . '.php';
})
->where('viewName', '[1-9][0-9]*');
```

Use the `filter` method to add a custom filter callback.
The filter callback must return either `true` or `false`.

```php
// Handle blog.1, blog.24, blog.2018 etc., 
// only when the template file exists
$renderer->handle('blog.{viewName}' function($viewName) {
    return '/some/path/to/blog' . $viewName . '.php';
})
->where('viewName', '[1-9][0-9]*')
->filter(function($viewName){
    return file_exists('/some/path/to/blog' . $viewName . '.php');
});
```

## Overriding views

Let's face it: no matter how awesome your website is, sooner or later you will want to
change how your site's content is presented to the users.
The classical workflow is to dig up into your template files, change the code inside them, 
and then publish the changes. 
While this seems to be the most logical and practical solution, it's far from being an ideal one.

Let's take into consideration, once again, the template for the `blog.article` view.

**article.php**

```php
<div class="article">
    <h2><?= $title ?></h2>
    <div><?= $content ?></div>
</div>
```

Our task is to change the `div`'s class attribute content to`blog-article`, and the
`h2` tag into an `h1` tag. Of course, we could make this changes directly into the *article.php*
template file, but remember, we don't want to take it on that road. Instead, we will create another
template file and make our changes there.

**custom-article.php**

```php
<div class="blog-article">
    <h1><?= $title ?></h1>
    <div><?= $content ?></div>
</div>
```

Now, we're ready to override our `blog.article` view. We do this
by adding a new rule, with the help of the `handle` method, 
specifying a higher priority for this rule than the priority of the one we wish to
override. The default priority for a handling rule is `0`.

```php
// Handle blog.article and blog.comment
$renderer->handle('blog.{viewName}' function($viewName) {
    return '/path/to/' . $viewName . '.php';
}) // default priority of 0
->whereIn('viewName', ['article', 'comment']);

// Override blog.article
$renderer->handle('blog.article' function() {
    return '/path/to/custom-article.php';
}, 1); // priority 1

// Test it
echo $renderer->renderView('blog.article', [
    'title' => 'Foo',
    'content' => 'Bar'
]);
```

**Result:**

```html
<div class="blog-article">
    <h1>Foo</h1>
    <div>Bar</div>
</div>
```
