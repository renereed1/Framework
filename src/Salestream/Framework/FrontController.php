<?php

namespace Salestream\Framework;

interface FrontController
{
    public function getRequest();
    
    public function getResponse();
    
    public function run();
}