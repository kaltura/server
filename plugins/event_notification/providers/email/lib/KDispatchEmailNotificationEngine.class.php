<?php
/**
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KDispatchEmailNotificationEngine extends KDispatchEventNotificationEngine
{
	/**
	 * Old kaltura default
	 * @var strung
	 */
	protected $defaultFromMail = 'notifications@kaltura.com';
	 
	/**
	 * Old kaltura default
	 * @var strung
	 */
	protected $defaultFromName = 'Kaltura Notification Service';
	
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::__construct()
	 */
	public function __construct(KSchedularTaskConfig $taskConfig, KalturaClient $client)
	{
		if(isset($taskConfig->params->defaultFromMail) && $taskConfig->params->defaultFromMail)
			$this->defaultFromMail = $taskConfig->params->defaultFromMail;
			
		if(isset($taskConfig->params->defaultFromName) && $taskConfig->params->defaultFromName)
			$this->defaultFromName = $taskConfig->params->defaultFromName;
			
		parent::__construct($taskConfig, $client);
	}
	
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::dispatch()
	 */
	public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData $data)
	{
		$this->sendEmail($eventNotificationTemplate, $data);
	}

	/**
	 * @param KalturaEmailNotificationTemplate $emailNotificationTemplate
	 * @param KalturaEmailNotificationDispatchJobData $data
	 * @return boolean
	 */
	public function sendEmail(KalturaEmailNotificationTemplate $emailNotificationTemplate, KalturaEmailNotificationDispatchJobData $data)
	{
		if(is_null($data->toEmail))
			throw new Exception("Recipient e-mail address cannot be null");
			
		$mailer = new PHPMailer();
		$mailer->CharSet = 'utf-8';
		$mailer->IsHTML($emailNotificationTemplate->format == KalturaEmailNotificationFormat::HTML);
		
		$mailer->AddAddress($data->toEmail, $data->toName);
		KalturaLog::info("Recipient [{$data->toName}<{$data->toEmail}>]");
			
		if(!is_null($data->fromEmail)) 
		{
			$mailer->Sender = $data->fromEmail;
			$mailer->From = $data->fromEmail;
			$mailer->FromName = $data->fromName;
		}
		else
		{
			$mailer->Sender = $this->defaultFromMail;
			$mailer->From = $this->defaultFromMail;
			$mailer->FromName = $this->defaultFromName;
		}
		KalturaLog::info("Sender [{$mailer->FromName}<{$mailer->From}>]");
		
		$contentParameters = array();
		foreach($data->contentParameters as $contentParameter)
		{
			/* @var $contentParameter KalturaKeyValue */
			$contentParameters[$contentParameter->key] = $contentParameter->value;
		}
		
		$subject = str_replace(array_keys($contentParameters), $contentParameters, $emailNotificationTemplate->subject);
		KalturaLog::info("Subject [$subject]");
		$mailer->Subject = $subject;
		
		$body = str_replace(array_keys($contentParameters), $contentParameters, $emailNotificationTemplate->body);
		KalturaLog::info("Body [$body]");
		$mailer->Body = $body;
		
		try
		{
			$success = $mailer->Send();
			if(!$success)
				throw new kTemporaryException("Sending mail failed: " . $mailer->ErrorInfo);
		}
		catch(Exception $e)
		{
			throw new kTemporaryException("Sending mail failed with exception: " . $e->getMessage(), $e->getCode());	
		}
			
		return true;
	}
}
