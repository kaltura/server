<?php
/**
 * @package UI-infra
 * @subpackage Client
 */
class Infra_ClientHelper
{
	private static $client = null;

	private static function hash($salt, $str)
	{
		return sha1($salt . $str);
	}

	public static function unimpersonate()
	{
		self::getClient()->setPartnerId(null);
	}

	public static function impersonate($partnerId)
	{
		self::getClient()->setPartnerId($partnerId);
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
		if (Infra_AuthHelper::getAuthInstance()->hasIdentity())
		{
			$ks = Infra_AuthHelper::getAuthInstance()->getIdentity()->getKs();
		}
		else
		{
			$ks = null;
		}

		return $ks;
	}

	/**
	 *
	 * @return Kaltura_Client_Client
	 */
	public static function getClient()
	{
		if(self::$client)
		{
			return self::$client;
		}

		if (!class_exists('Kaltura_Client_Client'))
			throw new Infra_Exception('Kaltura client not found, maybe it wasn\'t generated', Infra_Exception::ERROR_CODE_MISSING_CLIENT_LIB);

		$ks = self::getKs();

		$config = new Kaltura_Client_Configuration();
		$config->serviceUrl = self::getServiceUrl();
		$config->curlTimeout = self::getCurlTimeout();
		$config->setLogger(new Infra_ClientLoggingProxy());

		$settings = Zend_Registry::get('config')->settings;
		if(isset($settings->clientConfig))
		{
			foreach($settings->clientConfig as $attr => $value)
				$config->$attr = $value;
		}

		$front = Zend_Controller_Front::getInstance();
		$bootstrap = $front->getParam('bootstrap');
		if ($bootstrap)
		{
			$enviroment = $bootstrap->getApplication()->getEnvironment();
			if ($enviroment === 'development')
				$config->startZendDebuggerSession = true;
		}

		$client = new Kaltura_Client_Client($config);
		$client->setClientTag('Kaltura-' . $settings->applicationName);
		$client->setKs($ks);
		self::$client = $client;

		return $client;
	}
}