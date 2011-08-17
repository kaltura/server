<?php


class KalturaApiTestCase extends KalturaTestCaseBase implements IKalturaLogger
{
	/**
	 * @var KalturaClient
	 */
	protected $client;
	
	/**
	 * 
	 * Creates a new Kaltura API Test Case
	 * @param unknown_type $name
	 * @param array $data
	 * @param unknown_type $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '')
	{
		KalturaLog::debug("KalturaApiTestCase::__construct name [$name], data [" . print_r($data, true). "], dataName [$dataName]\n");
		
		parent::__construct($name, $data, $dataName);
				
		$testConfig = $this->config->get('config');
		$needSave = false;

		//TODO: add support for getting the values from the global data
		if(!$testConfig->serviceUrl)
		{
			$testConfig->serviceUrl = '@SERVICE_URL@';
			$needSave = true;
		}
		
		if(!$testConfig->partnerId)
		{
			$testConfig->partnerId = "@TEST_PARTNER_ID@";
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
		
		if(!isset($testConfig->startSession))
		{
			$testConfig->startSession = false;
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
				$testConfig->sessionType = 2;
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
	public function CompareAPIObjects(KalturaObjectBase $outputReference, KalturaObjectBase $actualResult, $validErrorFields = array())
	{
		//Use reflection to compare the objects
		$outputReferenceReflector = new ReflectionClass($outputReference);
		$properties = $outputReferenceReflector->getProperties(ReflectionProperty::IS_PUBLIC);
		
		$newErrors = array();
		
		foreach ($properties as $property)
		{
			$propertyName = $property->getName();
			
			//Start the php timer so we can gather performance data
			PHP_Timer::start();
			
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
	
	const HTTP_USER_AGENT = "\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6\"";
	
	/**
	 * 
	 * return the file output for a given url.
	 * used for api actions that return file.
	 * @param string $url url from which the answer is given.
	 */
	protected function getApiFileFromUrl($url)
	{
		$url = trim($url);
		$url = str_replace(array(' ', '[', ']'), array('%20', '%5B', '%5D'), $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, self::HTTP_USER_AGENT);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_NOSIGNAL, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec($ch);
	} 

	/**
	 * 
	 * Strip carriage return and new line from a string.
	 * @param string $string to be stripped.
	 */
	protected function stripWhiteSpaces($string)
	{
		$string = str_replace("\n", '', $string);
		$string = str_replace("\r", '', $string);
		return $string;
	} 

	/**
	 * Starts a new session
	 * @param KalturaSessionType $type
	 * @param string $userId
	 */
	protected function startSession($type, $userId = null)
	{
		print("start session\n");
		
		if($type == KalturaSessionType::ADMIN)
			$secret = KalturaGlobalData::getData("@TEST_PARTNER_ADMIN_SECRET@");
		else
			$secret = KalturaGlobalData::getData("@TEST_PARTNER_SECRET@");
				
		$ks = $this->client->session->start($secret, $userId, $type, $secret);
		$this->assertNotNull($ks);
		if (!$ks) {
			return false;
		}
		
		$this->client->setKs($ks);
		return true;
	}
}