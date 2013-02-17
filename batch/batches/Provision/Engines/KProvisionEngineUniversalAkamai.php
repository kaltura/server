<?php
/**
 * Provision Engine to provision new Akamai HLS+HDS live stream	
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KProvisionEngineUniversalAkamai extends KProvisionEngine
{
	public $taskConfig;
	
	public $systemUser;
	
	public $systemPassword;
	
	public $domainName;
	
	public static $baseServiceUrl;
	
	/**
	 * @var AkamaiUniversalStreamClient
	 */
	protected $streamClient;
	
	protected function __construct($taskConfig, KalturaAkamaiUniversalProvisionJobData $data)
	{
		if (!$taskConfig->params->restapi->akamaiRestApiBaseServiceUrl)
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: akamaiRestApiBaseServiceUrl is missing from worker configuration. Cannot provision stream"); 
		
		self::$baseServiceUrl = $taskConfig->params->restapi->akamaiRestApiBaseServiceUrl;
		parent::__construct($taskConfig);
		
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
			$this->systemUser = $taskConfig->params->restapi->systemUserName;
			$this->systemPassword = $taskConfig->params->restapi->systemPassword;
			$this->domainName = $taskConfig->params->restapi->domainName;
			$data->primaryContact = $taskConfig->params->restapi->primaryContact;
			$data->secondaryContact = $taskConfig->params->restapi->secondaryContact;
			$data->notificationEmail = $taskConfig->params->restapi->notificationEmail;
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
		/* @var $data KalturaAkamaiUniversalProvisionJobData */
		$streamConfiguration = new KalturaAkamaiUniversalStreamConfiguration();
		//Construct stream configuration
		$streamConfiguration->streamName = $data->streamName;
		$streamConfiguration->streamType = $data->streamType;
		$streamConfiguration->primaryContact = $data->primaryContact;
		$streamConfiguration->secondaryContact = $data->secondaryContact;
		$streamConfiguration->dvrEnabled = $data->dvrEnabled;
		$streamConfiguration->dvrWindow = $data->dvrWindow;
		$streamConfiguration->encoderPassword = $data->encoderPassword;
		$streamConfiguration->primaryEncodingIP = $data->encoderIP;
		$streamConfiguration->secondaryEncodingIP = $data->backupEncoderIP;
		$streamConfiguration->notificationEmail = $data->notificationEmail;
		
		$res = $this->streamClient->provisionStream($streamConfiguration);
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
		
		$data->streamID = $streamConfiguration->id;
		$data->primaryBroadcastingUrl = "rtmp://{$streamConfiguration->primaryEntryPoint}/EntryPoint";
		$data->secondaryBroadcastingUrl = "rtmp://{$streamConfiguration->secondaryEntryPoint}/EntryPoint";
		$data->encoderUsername = $streamConfiguration->encoderUserName;
		
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
	
	private function fromStreamXML (SimpleXMLElement $streamXML, KalturaAkamaiUniversalProvisionJobData $data)
	{
		$this->id = $this->getXMLNodeValue('stream-id', $xml);
		if (!$this->id)
		{
			throw new Exception("Necessary parameter stream-id missing from returned result");
		}
		
		$this->streamName = $this->getXMLNodeValue('stream-name', $xml);
		$encoderSettingsNodeName = 'encoder-settings';
		$encoderSettings = $xml->$encoderSettingsNodeName;
		$this->encoderUserName = strval($encoderSettings->username);
		if (!$this->encoderUserName)
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
				$this->secondaryEntryPoint = $domainName;
			}
			else
			{
				$this->primaryEntryPoint = $domainName;
			}
		}
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
		$url = self::$baseServiceUrl . "/{$this->domainName}/stream/".$data->streamID;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->systemUser}:{$this->systemPassword}");
		return curl_exec($ch);
		
	}

	
}