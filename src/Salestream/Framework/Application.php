<?php

namespace Salestream\Framework;

use Salestream\Framework\Http\Request;

class Application implements FrontController
{
    private $configuration;
    
    private $request;
    
    private $response;
    
    public function __construct(array $configuration = array())
    {
        $this->configuration = $configuration;
        $this->request = Request::createFromSuperGlobals();
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function run()
    {        
        $url = explode('/', $this->request->getUrl());
        
        $namespace = $this->configuration['namespace'];
        $controller = ucfirst($url[0]) . 'Controller';
        $action = lcfirst($url[1]) . 'Action';
        
        $class = $namespace . '\\' . $controller;
        
        if (!class_exists($class))
        {
            throw new \Exception('Class ' . $class . ' does not exist');
        }
        
        $class = new $class;
        
        if (!method_exists($class, $action))
        {
            throw new \Exception('Action ' . $action . ' does not exist in class ' . $controller);
        }
        
        unset($url[0]);
        unset($url[1]);
        
        $params = $url ? array_values($url) : [];
        
        $viewObject = call_user_func(array($class, $action), $params);
        
        $view_template = explode('/', $viewObject->getTemplate());
        
        $this->clean($viewObject->getData());
        
        $template = $this->configuration['path_to_views'] . $view_template[0] . '\\' . $view_template[1] . '.php';
        if (file_exists($template))
        {
            include $template;
        }
    }
    
    private function clean($str)
    {
        echo '<pre>';
        print_r($str);
        echo '</pre>';
    }
}

