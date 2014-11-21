<?php

namespace Salestream\Framework;

class RouteMatch
{
    private $parameters = [];
    
    public function findRoute($method, array $configuredRoutes = array(), array $urlComponents = array())
    {
        $urlComponents = array_values(array_filter(explode('/', $urlComponents['path'])));
        $urlComponentsCount = count($urlComponents);
        foreach ($configuredRoutes as $route)
        {
            $urlComponentsFromConfiguration = array_values(array_filter(explode('/', $route['url'])));
            $matchCount = $this->routeMatchCount($urlComponents, $urlComponentsFromConfiguration, $route, $method);
            
            if ($this->wasRouteFound($matchCount, $urlComponentsCount))
            {
                return array('route' => $route, 'parameters' => $this->parameters);
            }
        }
        return [];
    }
    
    public function routeMatchCount($urlComponents, $urlComponentsFromConfiguration, $route, $method)
    {
	$matchCount = -1;
	$urlComponentsCount = count($urlComponents);
	$urlComponentsFromConfigurationCount = count($urlComponentsFromConfiguration);

	if ($urlComponentsCount === $urlComponentsFromConfigurationCount && $route['method'] === $method)
	{
		$matchCount = $this->calculateRouteCount($urlComponents, $urlComponentsFromConfiguration);
	}
	return $matchCount;
    }
    
    public function calculateRouteCount($urlComponents, $urlComponentsFromConfiguration)
    {
        $matchCount = 0;
	$urlComponentsCount = count($urlComponents);
	for ($i = 0; $i < $urlComponentsCount; $i++)
	{
            if (false !== strpos($urlComponentsFromConfiguration[$i], '{:'))
            {
                array_push($this->parameters, $urlComponents[$i]);
                ++$matchCount;
            } else if ($urlComponents[$i] === $urlComponentsFromConfiguration[$i]) {
                ++$matchCount;
            }
	}
	return $matchCount;
    }
    
    public function wasRouteFound($matchCount, $urlComponentsCount)
    {
        if ($matchCount === $urlComponentsCount)
	{
            return true;
	}
	return false;
    }
}