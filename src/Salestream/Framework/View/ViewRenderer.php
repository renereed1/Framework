<?php

namespace Salestream\Framework\View;

class ViewRenderer
{
    private $template;
    
    private $data = [];
    
    public function __construct($template, array $data = array())
    {
        $this->template = $template;
        $this->data = $data;
    }
    
    /**
     * Calls a view template, and supplies data to be rendered in html.
     * 
     * @param type $template
     * @param type $data
     */
    public function renderView($render = true)
    {
        if ($render == false)
        {
            return;
        }
        if (!file_exists($this->template))
        {
            throw new \Exception('Template not found.');
        }
        extract($this->data, EXTR_OVERWRITE);
        
        $this->data = null;
        include_once $this->template;
    }
}
