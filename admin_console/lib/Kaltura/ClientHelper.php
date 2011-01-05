<?php
class Kaltura_ClientHelper 
{
	private static $client = null;
	
	private static function createKS($partnerId, $adminSecret, $sessionType = KalturaSessionType::ADMIN, $expiry = 7200)
	{
		$puserId = '';
		$privileges = '';
		
		$rand = rand(0, 32000);
		$rand = microtime(true);
		$expiry = time() + $expiry;
		$fields = array($partnerId, '', $expiry, $sessionType, $rand, $puserId, $privileges);
		$str = implode(";", $fields);
		
		$salt = $adminSecret;
		$hashed_str = self::hash($salt, $str) . "|" . $str;
		$decoded_str = base64_encode($hashed_str);
		
		return $decoded_str;
	}
	
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
		$settings = Zend_Registry::get('config')->settings;
		$partnerId = $settings->partnerId;
		$secret = $settings->secret;
		$sessionExpiry = $settings->sessionExpiry;
		$ks = self::createKS($partnerId, $secret, KalturaSessionType::ADMIN, $sessionExpiry);
		
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