<?php

namespace Salestream\Framework\View;

class View
{
    private $template;
    
    private $data = [];
    
    public function __construct($template = null, array $data = array())
    {
        $this->template = $template;
        $this->data = $data;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
    
    public function getData()
    {
        return $this->data;
    }
}