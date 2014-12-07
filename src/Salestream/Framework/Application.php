<?php

namespace Salestream\Framework;

use Salestream\Framework\Router;
use Salestream\Framework\View\ViewRenderer;

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
        try
        {
            $router = new Router($this->configuration);
            $viewObject = $router->dispatch();
        
            $template = $this->configuration['path_to_views'] . '/' . $viewObject['template'] . '.html.php';
        
            $viewRenderer = new ViewRenderer($template, $viewObject['view_data']);
            $render = true;
            if ($viewObject['template'] == '')
            {
                $render = false;
            }
            $viewRenderer->renderView($render);
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    }
}
