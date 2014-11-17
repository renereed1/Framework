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
    
    public function go()
    {
        $urlComponents = parse_url($this->url);
        
        $route = [];
        
        if (!array_key_exists('path', $urlComponents) || strlen($urlComponents['path']) < 2)
        {
            $route = $this->findDefaultRoute();
        } else {
            $route = $this->findDynamicRoute($urlComponents);
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
        
        $templateFolder = $route['controller'];
        
        $params = $this->parameters;
        
        $template = call_user_func_array(array($class, $action), $params);
        
        $templateFolder = $templateFolder . '/' . $template;
        
        return array(
            'template' => $templateFolder,
            'view_data' => $class->getAttributes(),
            'parameters' => $params
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
        $urlPath = explode('/', $urlComponents['path']);
        
        $urlPathCount = count($urlPath);
        
        $matchCount = 0;
        
        foreach ($this->configuration['routes'] as $route)
        {
            $urlPathFromConfiguration = array_values(array_filter(explode('/', $route['url'])));
            
            if ($urlPathCount === count($urlPathFromConfiguration) && $route['method'] === $this->request->getMethod())
            {
                // foreach ($urlPath as $path)
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
            }
            if ($matchCount === $urlPathCount)
            {
                return $route;
            }
        }
        return [];
    }
}