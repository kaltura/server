<?php
/**
 * Base Auth Micro Services Class
 * This is the base class for all services under 'plat-auth' repo
 */
abstract class MicroServiceAuthBase extends MicroServiceBaseService
{
	public static $host = 'auth';
	
	public function __construct()
	{
		$this->hostName = self::$host;
		parent::__construct();
	}

    public static function buildServiceUrl($hostName, $serviceName, $isApi = true)
    {
        $serviceUrl = parent::buildServiceUrl($hostName, $serviceName, $isApi);

        //remove the last suffix after the domain

        return $serviceUrl;
    }

}
