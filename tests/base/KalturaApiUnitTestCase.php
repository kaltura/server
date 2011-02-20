<?php

class KalturaApiUnitTestCase extends KalturaUnitTestCase implements IKalturaLogger
{
	/**
	 * @var KalturaClient
	 */
	protected $client;
	
	/**
	 * 
	 * Creates a new Kaltura API Unit Test Case
	 * @param unknown_type $name
	 * @param array $data
	 * @param unknown_type $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		
		$testConfig = $this->config->get('config');
		$needSave = false;
		
		if(!$testConfig->serviceUrl)
		{
			$testConfig->serviceUrl = 'http://www.kaltura.com/';
			$needSave = true;
		}
		if(!$testConfig->partnerId)
		{
			$testConfig->partnerId = 100;
			$needSave = true;
		}
		if(!$testConfig->clientTag)
		{
			$testConfig->clientTag = 'unitTest';
			$needSave = true;
		}
		if(!$testConfig->curlTimeout)
		{
			$testConfig->curlTimeout = 90;
			$needSave = true;
		}	
		if(!$testConfig->startSession)
		{
			$testConfig->startSession = true;
			$needSave = true;
		}		
		
		if($testConfig->startSession)
		{
			if(!$testConfig->secret)
			{
				$testConfig->secret = 'PARTNER_SECRET';
				$needSave = true;
			}
			if(!$testConfig->userId)
			{
				$testConfig->userId = '';
				$needSave = true;
			}
			if(!$testConfig->sessionType)
			{
				$testConfig->sessionType = 0;
				$needSave = true;
			}
			if(!$testConfig->expiry)
			{
				$testConfig->expiry = 60 * 60 * 24;
				$needSave = true;
			}
			if(!$testConfig->privileges)
			{
				$testConfig->privileges = '';
				$needSave = true;
			}
		}
		
		if($needSave)
			$this->config->saveToIniFile();
		
		$kalturaConfiguration = new KalturaConfiguration($testConfig->partnerId);
		$kalturaConfiguration->serviceUrl = $testConfig->serviceUrl;
		$kalturaConfiguration->clientTag = $testConfig->clientTag;
		$kalturaConfiguration->curlTimeout = $testConfig->curlTimeout;
		$kalturaConfiguration->setLogger($this);
		
		$this->client = new KalturaClient($kalturaConfiguration);
		
		if($testConfig->startSession)
		{
			$ks = $this->client->session->start($testConfig->secret, $testConfig->userId, $testConfig->sessionType, $testConfig->partnerId, $testConfig->expiry, $testConfig->privileges);
			$this->client->setKs($ks);
			KalturaLog::info("Session started [$ks]");
		}
	}
	
	/**
	 * Logs a given message
	 * @see IKalturaLogger::log()
	 */
	public function log($msg)
	{
		KalturaLog::log($msg);
	}

	/**
	 * 
	 * Compares two API objects and notify the PHPUnit and kaltura listeners 
	 * @param KalturaObjectBase $object1
	 * @param KalturaObjectBase $object2
	 */
	public static function CompareAPIObjects(KalturaObjectBase $outputReference, KalturaObjectBase $actualResult, $validErrorFields)
	{
		//Use reflection to compare the objects
		$outputReferenceReflector = new ReflectionClass($outputReference);
		$properties = $outputReferenceReflector->getProperties(ReflectionProperty::IS_PUBLIC);
		
		$newErrors = array();
		
		foreach ($properties as $property)
		{
			$propertyName = $property->getName();
			
			//Start the phpunit timer so we can gather performance data
			PHPUnit_Util_Timer::start();
			
			//If the field is in the valid failure list then we skip him 
			if(in_array($propertyName, $validErrorFields))
			{
				continue;
			}
			else 
			{
				$expectedValue = $property->getValue($outputReference);
				$actualValue = $property->getValue($actualResult);
				$assertToPerform = "assertEquals";

				$this->compareOnField($propertyName, $actualValue, $expectedValue, $assertToPerform);
				//if this is an array we need to change it to a string
			}
		}
	
		return $newErrors;
	}

	/**
	 * 
	 * Gets the parameters for creating a new kaltura client and returns the new client
	 * @param int $partnerId
	 * @param string $secret
	 * @param string $configServiceUrl
	 * @param int $isAdmin - 0 = no admin
	 * @return KalturaClient - a new api client 
	 */
	public function getClient($partnerId, $secret, $configServiceUrl, $isAdmin, $userId = null)
	{
		$config = new KalturaConfiguration((int)$partnerId);

		//Add the server url (into the test additional data)
		$config->serviceUrl = $configServiceUrl;
		$client = new KalturaClient($config);
		$sessionType = KalturaSessionType::USER;
		
		if($isAdmin != 0)
		{
			$sessionType =  KalturaSessionType::ADMIN;
		} 
		
		$ks = $client->session->start($secret, (string)$userId, $sessionType, (int)$partnerId, null, null);
		$client->setKs($ks);

		return $client;
	}
}