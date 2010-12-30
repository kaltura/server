<?php

class KalturaApiUnitTestCase extends KalturaUnitTestCase implements IKalturaLogger
{
	/**
	 * @var KalturaClient
	 */
	protected $client;
	
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
	
	public function log($msg)
	{
		KalturaLog::log($msg);
	}
}

