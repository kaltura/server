<?php
class Kaltura_ClientHelper 
{
	private static $client = null;
	
	private static function hash($salt, $str)
	{
		return sha1($salt . $str);
	}
	
	public static function unimpersonate()
	{
		$config = self::$client->getConfig();
		$config->partnerId = self::getPartnerId();
		self::$client->setConfig($config);
	}
	
	public static function impersonate($partnerId)
	{
		$config = self::$client->getConfig();
		$config->partnerId = $partnerId;
		self::$client->setConfig($config);
	}
	
	public static function getPartnerId()
	{
		$settings = Zend_Registry::get('config')->settings;
		return $settings->partnerId;
	}
	
	public static function getServiceUrl()
	{
		$settings = Zend_Registry::get('config')->settings;
		return $settings->serviceUrl;
	}
	
	public static function getCurlTimeout()
	{
		$settings = Zend_Registry::get('config')->settings;
		return $settings->curlTimeout;
	}
	
	public static function getKs()
	{
		if (Zend_Auth::getInstance()->hasIdentity())
		{
			$ks = Zend_Auth::getInstance()->getIdentity()->getKs();
		}
		else
		{
			$ks = null;
		}
		
		return $ks;
	}
	
	/**
	 * 
	 * @return KalturaClient
	 */
	public static function getClient()
	{
		if(self::$client)
			return self::$client;
			
		$partnerId = self::getPartnerId();
		$ks = self::getKs();
		
		$config = new KalturaConfiguration($partnerId);
		$config->serviceUrl = self::getServiceUrl();
		$config->curlTimeout = self::getCurlTimeout();
		$config->setLogger(new Kaltura_ClientLoggingProxy());
		$front = Zend_Controller_Front::getInstance();
		$bootstrap = $front->getParam('bootstrap');
		if ($bootstrap) 
		{
			$enviroment = $bootstrap->getApplication()->getEnvironment();
			if ($enviroment === 'development')
				$config->startZendDebuggerSession = true;
		}
		
		$client = new KalturaClient($config);
		$client->setKs($ks);
			
		self::$client = $client;
		
		return $client;
	}
}