<?php

namespace Salestream\Framework;

use Salestream\Framework\Http\Request;

class Router
{
    private $url;
    
    private $request;
    
    private $configuration;
    
    private $parameters = [];
    
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
        $this->request = Request::createFromSuperGlobals();
        $this->url = $this->explodeUrl();
    }
    
    public function dispatch()
    {
        $urlComponents = parse_url($this->url);
        
        $route = [];
        
        if (!array_key_exists('path', $urlComponents) || strlen($urlComponents['path']) < 2)
        {
            $route = $this->findDefaultRoute();
        } else {
            $route = $this->findDynamicRoute($urlComponents);
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
        
        $template = call_user_func_array(array($class, $action), $this->parameters);
        
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
    
    /**
     * Find the default route. This route should be used for root based url.
     * 
     * @return array
     * @return null
     */
    private function findDefaultRoute()
    {
        foreach ($this->configuration['routes'] as $route)
        {
            if (array_key_exists('default', $route))
            {
                return $route;
            }
        }
        return null;
    }
    
    private function findDynamicRoute($urlComponents)
    {
        $urlPath = array_values(array_filter(explode('/', $urlComponents['path'])));
        
        $urlPathCount = count($urlPath);
        
        $matchCount = 0;
        
        foreach ($this->configuration['routes'] as $route)
        {
            $matchCount = 0;
            $urlPathFromConfiguration = array_values(array_filter(explode('/', $route['url'])));;
            
            if ($urlPathCount === count($urlPathFromConfiguration) && $route['method'] === $this->request->getMethod())
            {
                if ($route['type'] === 'static')
                {
                    for ($i = 0; $i < $urlPathCount; $i++)
                    {
                        if ($urlPath[$i] === $urlPathFromConfiguration[$i] && $urlPath[$i] !== '')
                        {
                            ++$matchCount;
                        }
                    }
                    
                    if ($matchCount === $urlPathCount)
                    {
                        return $route;
                    }
                    
                } else {
                    for ($i = 0; $i < $urlPathCount; $i++)
                    {
                        if (false !== strpos($urlPathFromConfiguration[$i], '{:'))
                        {
                            array_push($this->parameters, $urlPath[$i]);
                            ++$matchCount;
                        } else if ($urlPath[$i] === $urlPathFromConfiguration[$i]) {
                            ++$matchCount;
                        }
                    }
                    
                    if ($matchCount == $urlPathCount)
                    {
                        return $route;
                    }
                }
            }
        }
        return [];
    }
}