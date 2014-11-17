<?php

namespace Salestream\Framework;

use Salestream\Framework\Router;

class Application implements FrontController
{
    private $configuration;
    
    public function __construct(array $configuration = array())
    {
        $this->configuration = $configuration;
    }
    
    /**
     * Run the application.
     */
    public function run()
    {
        $router = new Router($this->configuration);
        $viewObject = $router->dispatch();
        
        $template = $this->configuration['path_to_views'] . '/' . $viewObject['template'] . '.php';

        if (file_exists($template))
        {
            $this->renderView($template, $viewObject['view_data']);
        }
    }
    
    /**
     * Calls a view template, and supplies data to be rendered in html.
     * 
     * @param type $template
     * @param type $data
     */
    public function renderView($template, $data)
    {
        foreach ($data as $value)
        {
            extract($value, EXTR_OVERWRITE);
        }
        $data = [];
        include_once $template;
    }
}