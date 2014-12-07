<?php

namespace Salestream\Framework\Controller;
use Salestream\Framework\Http\Request;

abstract class Controller
{
    private $attributes = [];
    
    private $request;
    
    public function __construct()
    {
        $this->request = Request::createFromSuperGlobals();
    }
    
    /**
     * Add attributes to pass to the view.
     * 
     * @param type $key
     * @param type $data
     */
    public function addAttribute($key, $data)
    {
        if ($key === '' || $data === '')
        {
            return;
        }
        $this->attributes[$key] = $data;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function getPostData()
    {
        return $_POST;
    }
    
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            return true;
        }
        return false;
    }
}
