<?php

namespace Salestream\Framework;

interface FrontController
{
    /**
     * Calls a view template, and supplies data to be rendered in html.
     * 
     * @param type $template
     * @param type $data
     */
    public function renderView($template, $data);
    
    /**
     * Run the application.
     */
    public function run();
}