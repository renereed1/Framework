<?php

namespace Salestream\Framework\DataSource;

class Database
{
    protected $configuration;
    
    private $handler;
    
    public function __construct(array $configuration = array())
    {
        $this->configuration = $configuration['database'];
        
        $this->handler = new \PDO('mysql:host=' . $this->configuration['host'] . ';dbname=' . $this->configuration['db'], $this->configuration['user'], $this->configuration['pass']);
    }
    
    public function __destruct()
    {
        $this->handler = null;
    }
    
    public function getHandler()
    {
        return $this->handler;
    }
}