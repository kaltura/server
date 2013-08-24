<?php
/**
 * Provision Engine to provision new Akamai HLS+HDS live stream	
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KProvisionEngineUniversalAkamai extends KProvisionEngine
{
	public $systemUser;
	
	public $systemPassword;
	
	public $domainName;
	
	public static $baseServiceUrl;
	
	const PROVISIONED = 'Provisioned';
	
	const PENDING = 'Pending';
	
	/**
	 * @var AkamaiUniversalStreamClient
	 */
	protected $streamClient;
	
	protected function __construct(KalturaAkamaiUniversalProvisionJobData $data)
	{
		if (!KBatchBase::$taskConfig->params->restapi->akamaiRestApiBaseServiceUrl)
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: akamaiRestApiBaseServiceUrl is missing from worker configuration. Cannot provision stream"); 
		
		self::$baseServiceUrl = KBatchBase::$taskConfig->params->restapi->akamaiRestApiBaseServiceUrl;
		
		$username = null;
		$password = null;
		
		if (!is_null($data) && $data instanceof KalturaAkamaiUniversalProvisionJobData)
		{
			//all fields are set and are not empty string
			if ($data->systemUserName && $data->systemPassword && $data->domainName)
			{ 
				$this->systemUser = $data->systemUserName;
				$this->systemPassword = $data->systemPassword;
				$this->domainName = $data->domainName;
			}
		}
		//if one of the params was not set, use the taskConfig data	
		if (!$username || !$password )
		{
			$this->systemUser = KBatchBase::$taskConfig->params->restapi->systemUserName;
			$this->systemPassword = KBatchBase::$taskConfig->params->restapi->systemPassword;
			$this->domainName = KBatchBase::$taskConfig->params->restapi->domainName;
			$data->primaryContact = KBatchBase::$taskConfig->params->restapi->primaryContact;
			$data->secondaryContact = KBatchBase::$taskConfig->params->restapi->secondaryContact;
			$data->notificationEmail = KBatchBase::$taskConfig->params->restapi->notificationEmail;
		}
	}
	
	/* (non-PHPdoc)
	 * @see KProvisionEngine::getName()
	 */
	public function getName() {
		return get_class($this);
		
	}

	/* (non-PHPdoc)
	 * @see KProvisionEngine::provide()
	 */
	public function provide(KalturaBatchJob $job, KalturaProvisionJobData $data) 
	{
		$res = $this->provisionStream($data);
		if (!$res)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: no result received for connection"); 
		}
		
		KalturaLog::info ("Request to provision stream returned result: $res");
		$resultXML = new SimpleXMLElement($res);
		//In this case, REST API has returned an API error.
		$errors = $resultXML->xpath('error');
		if ($errors && count($errors))
		{
			//There is always only 1 error listed in the XML
			$error = $errors[0];
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: ". strval($error[0]));
		}
		//Otherwise, the stream provision request probably returned OK, attempt to parse it as a new stream XML
		try {
			$data = $this->fromStreamXML($resultXML, $data);
		}
		catch (Exception $e)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: ". $e->getMessage());
		}

		return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, 'Succesfully provisioned entry', $data);
		
	}
	
	/**
	 * Function to provision the stream using the Akamai RestAPI
	 * @param KalturaAkamaiUniversalProvisionJobData $data
	 * @return mixed
	 */
	private function provisionStream (KalturaAkamaiUniversalProvisionJobData $data)
	{
		$url = self::$baseServiceUrl . "/{$this->domainName}/stream";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getStreamXML($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->systemUser}:{$this->systemPassword}");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml'));
		return curl_exec($ch);
	}
	
	/**
	 * Construct stream using the job data
	 * @param KalturaAkamaiUniversalProvisionJobData $data
	 * @return string
	 */
	private function getStreamXML (KalturaAkamaiUniversalProvisionJobData $data)
	{
		$result = new SimpleXMLElement("<stream/>");
		$result->addChild("stream-type", $data->streamType);
		$result->addChild("stream-name", $data->streamName);
		$result->addChild("primary-contact-name", $data->primaryContact);
		$result->addChild("secondary-contact-name", $data->secondaryContact);
		$result->addChild("notification-email", $data->notificationEmail);
		
		$encoderSettings = $result->addChild("encoder-settings");
		$encoderSettings->addChild("primary-encoder-ip", $data->encoderIP);
		$encoderSettings->addChild("backup-encoder-ip", $data->backupEncoderIP);
		$encoderSettings->addChild("password", $data->encoderPassword);
		
		$dvrSettings = $result->addChild("dvr-settings");
		$dvrSettings->addChild("dvr", $data->dvrEnabled ? "Enabled" : "Disabled");
		$dvrSettings->addChild("dvr-window", $data->dvrWindow);
		
		return $result->saveXML();
	}
	
	private function fromStreamXML (SimpleXMLElement $xml, KalturaAkamaiUniversalProvisionJobData $data)
	{
		$data->streamID = $this->getXMLNodeValue('stream-id', $xml);
		if (!$data->streamID)
		{
			throw new Exception("Necessary parameter stream-id missing from returned result");
		}
		
		$data->streamName = $this->getXMLNodeValue('stream-name', $xml);
		$encoderSettingsNodeName = 'encoder-settings';
		$encoderSettings = $xml->$encoderSettingsNodeName;
		$data->encoderUsername = strval($encoderSettings->username);
		if (!$data->encoderUsername)
		{
			throw new Exception("Necessary parameter [username] missing from returned result");
		}		
		//Parse encoding primary and secondary entry points
		$entryPoints = $xml->xpath('/stream/entrypoints/entrypoint');
		if (!$entryPoints || !count($entryPoints))
			throw new Exception('Necessary configurations for entry points missing from the returned result');
			
		foreach ($entryPoints as $entryPoint)
		{
			/* @var $entryPoint SimpleXMLElement */
			$domainNodeName = 'domain-name';
			$domainName = $entryPoint->$domainNodeName;
			if (!$domainName)
			{
				throw new Exception('Necessary URL for entry point missing from the returned result');
			}
			if (strval($entryPoint->type) == 'Backup')
			{
				$data->secondaryBroadcastingUrl = "rtmp://".$domainName . "/EntryPoint";
			}
			else
			{
				$data->primaryBroadcastingUrl = "rtmp://". $domainName . "/EntryPoint";
			}
		}
		
		return $data;
	}
	
	/**
	 * @param string $nodeName
	 * @param SimpleXMLElement $xml
	 * @return string
	 */
	private function getXMLNodeValue ($nodeName, SimpleXMLElement $xml)
	{
		return strval($xml->$nodeName);
	}
	
	/* (non-PHPdoc)
	 * @see KProvisionEngine::delete()
	 */
	public function delete(KalturaBatchJob $job, KalturaProvisionJobData $data) 
	{
		KalturaLog::info("Deleting stream with ID [". $data->streamID ."]" );
		
		$url = self::$baseServiceUrl . "/{$this->domainName}/stream/".$data->streamID;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->systemUser}:{$this->systemPassword}");
		$result = curl_exec($ch);
		
		if (!$result)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: failed to call RestAPI");
		}
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode<=200 && $httpCode>300)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: delete failed");
		}
		
		return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, 'Succesfully deleted stream', $data);
	}
	

	/* (non-PHPdoc)
	 * @see KProvisionEngine::checkProvisionedStream()
	 */
	public function checkProvisionedStream(KalturaBatchJob $job, KalturaProvisionJobData $data) 
	{
		KalturaLog::info("Retrieving stream with ID [". $data->streamID ."]" );
		
		$url = self::$baseServiceUrl . "/{$this->domainName}/stream/".$data->streamID;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->systemUser}:{$this->systemPassword}");
		$result = curl_exec($ch);
		
		if (!$result)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: failed to call RestAPI");
		}
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode<=200 && $httpCode>300)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: retrieval failed");
		}
		
		KalturaLog::info("Result received: $result");
		$resultXML = new SimpleXMLElement($result);
		$errors = $resultXML->xpath('error');
		if ($errors && count($errors))
		{
			//There is always only 1 error listed in the XML
			$error = $errors[0];
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: ". strval($error[0]));
		}
		
		if ($resultXML->status)
		{
			switch (strval($resultXML->status))
			{
				case self::PENDING:
					return new KProvisionEngineResult(KalturaBatchJobStatus::ALMOST_DONE, "Stream is still in status Pending - retry in 5 minutes");
					break;
				case self::PROVISIONED:
					return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, "Stream is in status Provisioned");
					break;
			}
		}
		
		return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Unable to retrieve valid status from result of Akamai REST API");
	}

	
}