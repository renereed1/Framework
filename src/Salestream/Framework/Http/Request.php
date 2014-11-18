<?php

namespace Salestream\Framework\Http;

class Request
{
    private $query;
    
    private $request;
    
    private $attributes;
    
    private $server;
    
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $server = array())
    {
        $this->query = $query;
        $this->request = $request;
        $this->attributes = $attributes;
        $this->server = $server;
    }
    
    public function getUrl()
    {
        $url = rtrim($this->server['REQUEST_URI'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);    
        return $url;
    }
    
    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }
    
    public static function createFromSuperGlobals(array $query = array(), array $request = array(), array $attributes = array(), array $server = array())
    {
        return new Request($_GET, $_POST, array(), $_SERVER);
    }
    
    public function getPost()
    {
        return $this->request;
    }
    
    public function isPost()
    {
        return 'POST' == $this->server ? true : false;
    }
}