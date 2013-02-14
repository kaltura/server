<?php
/**
 * Provision Engine to provision new Akamai HLS+HDS live stream	
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KProvisionEngineUniversalAkamai extends KProvisionEngine
{
	/**
	 * @var AkamaiUniversalStreamClient
	 */
	protected $streamClient;
	
	protected function __construct($taskConfig, KalturaAkamaiUniversalProvisionJobData $data)
	{
		if (!$this->taskConfig->params->restapi->akamaiRestApiBaseServiceUrl)
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: akamaiRestApiBaseServiceUrl is missing from worker configuration. Cannot provision stream"); 
		
		AkamaiUniversalStreamClient::$baseServiceUrl = $this->taskConfig->params->akamaiRestApiBaseServiceUrl;
		parent::__construct($taskConfig);
		
		$username = null;
		$password = null;
		
		if (!is_null($data) && $data instanceof KalturaAkamaiUniversalProvisionJobData)
		{
			//all fields are set and are not empty string
			if ($data->systemUserName && $data->systemPassword && $data->domainName)
			{
				$username = $data->systemUserName;
				$password = $data->systemPassword;
				$domainName = $data->domainName;
			}
		}
		//if one of the params was not set, use the taskConfig data	
		if (!$username || !$password )
		{
			$username = $this->taskConfig->params->restapi->systemUserName;
			$password = $this->taskConfig->params->restapi->systemPassword;
			$domainName = $this->taskConfig->params->restapi->domainName;
			$data->primaryContact = $this->taskConfig->restapi->primaryContact;
			$data->secondaryContact = $this->taskConfig->restapi->secondaryContact;
			$data->notificationEmail = $this->taskConfig->restapi->notificationEmail;
		}
		
		KalturaLog::debug("Connecting to Akamai(username: $username, password: $password, domain: $domainName)");
		$this->streamClient = new AkamaiUniversalStreamClient($username, $password, $domainName);
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
			$streamConfiguration->fromXML($resultXML);
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

	/* (non-PHPdoc)
	 * @see KProvisionEngine::delete()
	 */
	public function delete(KalturaBatchJob $job, KalturaProvisionJobData $data) {
		// TODO Auto-generated method stub
		
	}

	
}