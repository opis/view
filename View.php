<?php

namespace Opis\View;

use Closure;
use Opis\View\Routing\ViewCollection;
use Opis\View\Routing\ViewRoute;
use Opis\View\Routing\ViewRouter;

class View
{
    
    protected $resolver;
    
    protected $collection;
    
    protected $insertKey;
    
    protected $viewKey;
    
    public function __construct(EngineResolver $resolver = null, ViewCollection $collection = null, $insertKey = true, $viewkey = 'view')
    {
        if($resolver === null)
        {
            $resolver = new EngineResolver();
        }
        
        if($collection === null)
        {
            $collection = new ViewCollection();
        }
        
        $this->resolver = $resolver;
        $this->collection = $collection;
        $this->insertKey = (bool) $insertKey;
        $this->viewKey = (string) $viewkey;
    }
    
    public function registerEngineResolver($extension, Closure $resolver)
    {
        $this->resolver->register($extension, $resolver);
    }
    
    public function handle($pattern, Closure $callback, $priority = 0)
    {
        return $this->collection->add(new ViewRoute($pattern, $callback), $priority);
    }
    
    public function render($view)
    {
        if(!($view instanceof ViewableInterface))
        {
            return (string) $view;
        }
        $router = new ViewRouter($view->viewName(), $this->collection);
        $path = $router->execute();
        $engine = $this->resolver->resolve($path);
        $arguments = $view->viewArguments();
        if($this->insertKey)
        {
            $arguments[$this->viewKey] = $this;
        }
        return $engine->build($path, $arguments);
    }
    
}