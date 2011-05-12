<?php
/**
 * base class for the real ProvisionEngine in the system - currently only akamai 
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KProvisionEngineAkamai extends KProvisionEngine
{
	/**
	 * @var AkamaiStreamsClient
	 */
	private $streamClient;

	/**
	 * @return string
	 */
	public function getName()
	{
		return get_class($this);
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	protected function __construct( KSchedularTaskConfig $taskConfig )
	{
		parent::__construct($taskConfig);
		
		$username = $this->taskConfig->params->wsdlUsername;
		$password = $this->taskConfig->params->wsdlPassword;
		
		KalturaLog::debug("Connecting to Akamai(username: $username, password: $password)");
		$this->streamClient = new AkamaiStreamsClient($username, $password);
	}
	
	/* (non-PHPdoc)
	 * @see batches/Provision/Engines/KProvisionEngine#provide()
	 */
	public function provide( KalturaBatchJob $job, KalturaProvisionJobData $data )
	{
		
		$cpcode = $this->taskConfig->params->cpcode;
		$emailId = $this->taskConfig->params->emailId;
		$primaryContact = $this->taskConfig->params->primaryContact;
		$secondaryContact = $this->taskConfig->params->secondaryContact;
		
		$name = $job->entryId;
		$encoderIP = $data->encoderIP;
		$backupEncoderIP = $data->backupEncoderIP;
		$encoderPassword = $data->encoderPassword;
		$endDate = $data->endDate;
		$dynamic = true;
		
		KalturaLog::debug("provideEntry(encoderIP: $encoderIP, backupEncoderIP: $backupEncoderIP, encoderPassword: $encoderPassword, endDate: $endDate)");
		$flashLiveStreamInfo = $this->streamClient->provisionFlashLiveDynamicStream($cpcode, $name, $encoderIP, $backupEncoderIP, $encoderPassword, $emailId, $primaryContact, $secondaryContact, $endDate, $dynamic);
		
		if(!$flashLiveStreamInfo)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: " . $this->streamClient->getError());
		}
		
		foreach($flashLiveStreamInfo as $field => $value)
			KalturaLog::info("Returned $field => $value");
				
		if(isset($flashLiveStreamInfo['faultcode']))
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: " . $flashLiveStreamInfo['faultstring']);
		}
		
		$arr = null;
		if(preg_match('/p\.ep(\d+)\.i\.akamaientrypoint\.net/', $flashLiveStreamInfo['primaryEntryPoint'], $arr))
			$data->streamID = $arr[1];
			
		if(preg_match('/b\.ep(\d+)\.i\.akamaientrypoint\.net/', $flashLiveStreamInfo['backupEntryPoint'], $arr))
			$data->backupStreamID = $arr[1];
			
		$data->rtmp = $flashLiveStreamInfo['connectUrl'];
		$data->encoderUsername = $flashLiveStreamInfo['encoderUsername'];
		$data->primaryBroadcastingUrl = 'rtmp://'.$flashLiveStreamInfo['primaryEntryPoint'].'/EntryPoint';
		$data->secondaryBroadcastingUrl = 'rtmp://'.$flashLiveStreamInfo['backupEntryPoint'].'/EntryPoint';
		$tempStreamName = explode('@', $flashLiveStreamInfo['streamName']);
		if (count($tempStreamName) == 2) {
			$data->streamName = $tempStreamName[0] . '_%i@' . $tempStreamName[1];
		}
		else {
			$data->streamName = $flashLiveStreamInfo['streamName'];
		}
		
		
		return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, 'Succesfully provisioned entry', $data);
	}
	
	/* (non-PHPdoc)
	 * @see batches/Provision/Engines/KProvisionEngine#delete()
	 */
	public function delete( KalturaBatchJob $job, KalturaProvisionJobData $data )
	{
		KalturaLog::debug("delete (streamID: $data->streamID)");
		$returnVal = $this->streamClient->deleteStream($data->streamID, true);
		
		if(!$returnVal)
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: " . $this->streamClient->getError());
		}
		
		if(is_array($returnVal))
		{
			foreach($returnVal as $field => $value)
				KalturaLog::info("Returned $field => $value");
		}
		else
		{
			KalturaLog::info("Returned: $returnVal");
		}
				
		if(isset($returnVal['faultcode']))
		{
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: " . $returnVal['faultstring']);
		}
		
		$data->returnVal = $returnVal;
		return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, 'Succesfully deleted entry', $data);
	}
}

