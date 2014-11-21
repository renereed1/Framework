<?php

namespace Salestream\Framework;

use Salestream\Framework\Http\Request;

class Router
{
    private $url;
    
    private $request;
    
    private $configuration;
    
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
        $this->request = Request::createFromSuperGlobals();
        $this->url = $this->explodeUrl();
    }
    
    public function dispatch()
    {
        $urlComponents = parse_url($this->url);
        
        $routeMatch = new RouteMatch();
        $routeMatched = $routeMatch->findRoute($this->request->getMethod(), $this->configuration['routes'], $urlComponents);
        
        if (!array_key_exists('route', $routeMatched))
        {
            throw new \Exception();
        }
        
        $route = $routeMatched['route'];
        
        if (!array_key_exists('parameters', $routeMatched))
        {
            $parameters = [];
        } else {
            $parameters = $routeMatched['parameters'];
        }
        
        if (!array_key_exists('controller', $route) || !array_key_exists('action', $route))
        {
            throw new \Exception('Url ' . $this->url . ' could not be found in the routing system. Please check configuration.');
        }
        
        $namespace = $this->configuration['namespace'];
        $controller = ucfirst($route['controller']) . 'Controller';
        $action = lcfirst($route['action']) . 'Action';
        
        $class = $namespace . '\\' . $controller;
        
        if (!class_exists($class))
        {
            throw new \Exception('Class ' . $class . ' does not exist.');
        }
        
        $class = new $class;
        
        if (!method_exists($class, $action))
        {
            throw new \Exception('Action ' . $action . ' does not exist in class ' . $controller);
        }
        
        $template = call_user_func_array(array($class, $action), $parameters);
        
        if ($template == '')
        {
            $templateFolder = '';
        } else {
            $templateFolder = $route['controller'] . '/' . $template;
        }
        
        return array(
            'template' => $templateFolder,
            'view_data' => $class->getAttributes()
        );
    }
    
    private function explodeUrl()
    {
        return $this->request->getUrl();
    }
}