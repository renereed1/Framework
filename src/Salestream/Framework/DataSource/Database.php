<?php

namespace Salestream\Framework\DataSource;

class Database
{
    protected $configuration;
    
    private $handler;
    
    public function __construct(array $configuration = array())
    {
        $this->configuration = $configuration['database'];
        
        $this->handler = new \PDO('mysql:host=' . $this->configuration['host'] . ';dbname=' . $this->configuration['db'] . ';charset=utf8', $this->configuration['user'], $this->configuration['pass']);
        $this->handler->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->handler->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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