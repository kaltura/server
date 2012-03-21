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
	
	/**
	 * @var PHPMailer
	 */
	static protected $mailer = null;
	
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::__construct()
	 */
	public function __construct(KSchedularTaskConfig $taskConfig, KalturaClient $client)
	{
		if(isset($taskConfig->params->defaultFromMail) && $taskConfig->params->defaultFromMail)
			$this->defaultFromMail = $taskConfig->params->defaultFromMail;
			
		if(isset($taskConfig->params->defaultFromName) && $taskConfig->params->defaultFromName)
			$this->defaultFromName = $taskConfig->params->defaultFromName;

		if(!$this::$mailer)
		{
			$this::$mailer = new PHPMailer();
			$this::$mailer->CharSet = 'utf-8';
			$this::$mailer->SMTPKeepAlive = true;
		
			if(isset($taskConfig->params->mailPriority) && $taskConfig->params->mailPriority)
				$this::$mailer->Priority = 	$taskConfig->params->mailPriority;
				
			if(isset($taskConfig->params->mailCharSet) && $taskConfig->params->mailCharSet)
				$this::$mailer->CharSet = 	$taskConfig->params->mailCharSet;
				
			if(isset($taskConfig->params->mailContentType) && $taskConfig->params->mailContentType)
				$this::$mailer->ContentType = 	$taskConfig->params->mailContentType;
				
			if(isset($taskConfig->params->mailEncoding) && $taskConfig->params->mailEncoding)
				$this::$mailer->Encoding = 	$taskConfig->params->mailEncoding;
				
			if(isset($taskConfig->params->mailWordWrap) && $taskConfig->params->mailWordWrap)
				$this::$mailer->WordWrap = 	$taskConfig->params->mailWordWrap;
				
			if(isset($taskConfig->params->mailMailer) && $taskConfig->params->mailMailer)
				$this::$mailer->Mailer = 	$taskConfig->params->mailMailer;
				
			if(isset($taskConfig->params->mailSendmail) && $taskConfig->params->mailSendmail)
				$this::$mailer->Sendmail = 	$taskConfig->params->mailSendmail;
				
			if(isset($taskConfig->params->mailSmtpHost) && $taskConfig->params->mailSmtpHost)
				$this::$mailer->Host = 	$taskConfig->params->mailSmtpHost;
				
			if(isset($taskConfig->params->mailSmtpPort) && $taskConfig->params->mailSmtpPort)
				$this::$mailer->Port = 	$taskConfig->params->mailSmtpPort;
				
			if(isset($taskConfig->params->mailSmtpHeloMessage) && $taskConfig->params->mailSmtpHeloMessage)
				$this::$mailer->Helo = 	$taskConfig->params->mailSmtpHeloMessage;
				
			if(isset($taskConfig->params->mailSmtpSecure) && $taskConfig->params->mailSmtpSecure)
				$this::$mailer->SMTPSecure = 	$taskConfig->params->mailSmtpSecure;
				
			if(isset($taskConfig->params->mailSmtpAuth) && $taskConfig->params->mailSmtpAuth)
				$this::$mailer->SMTPAuth = 	$taskConfig->params->mailSmtpAuth;
				
			if(isset($taskConfig->params->mailSmtpUsername) && $taskConfig->params->mailSmtpUsername)
				$this::$mailer->Username = 	$taskConfig->params->mailSmtpUsername;
				
			if(isset($taskConfig->params->mailSmtpPassword) && $taskConfig->params->mailSmtpPassword)
				$this::$mailer->Password = 	$taskConfig->params->mailSmtpPassword;
				
			if(isset($taskConfig->params->mailSmtpTimeout) && $taskConfig->params->mailSmtpTimeout)
				$this::$mailer->Timeout = 	$taskConfig->params->mailSmtpTimeout;
				
			if(isset($taskConfig->params->mailSmtpTimeout) && $taskConfig->params->mailSmtpTimeout)
				$this::$mailer->Timeout = 	$taskConfig->params->mailSmtpTimeout;
				
			if(isset($taskConfig->params->mailSmtpKeepAlive) && $taskConfig->params->mailSmtpKeepAlive)
				$this::$mailer->SMTPKeepAlive = 	$taskConfig->params->mailSmtpKeepAlive;
				
			if(isset($taskConfig->params->mailXMailerHeader) && $taskConfig->params->mailXMailerHeader)
				$this::$mailer->XMailer = 	$taskConfig->params->mailXMailerHeader;
				
			if(isset($taskConfig->params->mailErrorMessageLanguage) && $taskConfig->params->mailErrorMessageLanguage)
				$this::$mailer->SetLanguage($taskConfig->params->mailErrorMessageLanguage);
		}
		
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
		if(!count($data->to) && !count($data->cc) && !count($data->bcc))
			throw new Exception("Recipient e-mail address cannot be null");
			
		$this::$mailer->IsHTML($emailNotificationTemplate->format == KalturaEmailNotificationFormat::HTML);
		
		if($data->priority)
			$this::$mailer->Priority = 	$data->priority;
		if($data->confirmReadingTo)
			$this::$mailer->ConfirmReadingTo = $data->confirmReadingTo;
		if($data->hostname)
			$this::$mailer->Hostname = $data->hostname;
		if($data->messageID)
			$this::$mailer->MessageID = $data->messageID;
		
		if(is_array($data->to))
		{
			foreach ($data->to as $to)
			{
				/* @var $to KalturaKeyValue */
				$email = $to->key;
				$name = $to->value;
				KalturaLog::info("Add TO recipient [$email<$name>]");
				$this::$mailer->AddAddress($email, $name);
			}
		}
		
		if(is_array($data->cc))
		{
			foreach ($data->cc as $to)
			{
				/* @var $to KalturaKeyValue */
				$email = $to->key;
				$name = $to->value;
				KalturaLog::info("Add CC recipient [$email<$name>]");
				$this::$mailer->AddCC($email, $name);
			}
		}
		
		if(is_array($data->bcc))
		{
			foreach ($data->bcc as $to)
			{
				/* @var $to KalturaKeyValue */
				$email = $to->key;
				$name = $to->value;
				KalturaLog::info("Add BCC recipient [$email<$name>]");
				$this::$mailer->AddBCC($email, $name);
			}
		}
		
		if(is_array($data->replyTo))
		{
			foreach ($data->replyTo as $to)
			{
				/* @var $to KalturaKeyValue */
				$email = $to->key;
				$name = $to->value;
				KalturaLog::info("Add REPLY-TO recipient [$email<$name>]");
				$this::$mailer->AddReplyTo($email, $name);
			}
		}
			
		if(!is_null($data->fromEmail)) 
		{
			$this::$mailer->Sender = $data->fromEmail;
			$this::$mailer->From = $data->fromEmail;
			$this::$mailer->FromName = $data->fromName;
		}
		else
		{
			$this::$mailer->Sender = $this->defaultFromMail;
			$this::$mailer->From = $this->defaultFromMail;
			$this::$mailer->FromName = $this->defaultFromName;
		}
		KalturaLog::info("Sender [{$this::$mailer->FromName}<{$this::$mailer->From}>]");
		
		$subject = $emailNotificationTemplate->subject;
		$body = $emailNotificationTemplate->body;
		
		$contentParameters = array();
		if(is_array($data->contentParameters) && count($data->contentParameters))
		{
			foreach($data->contentParameters as $contentParameter)
			{
				/* @var $contentParameter KalturaKeyValue */
				$contentParameters['{' . $contentParameter->key . '}'] = $contentParameter->value;
			}
		
			$subject = str_replace(array_keys($contentParameters), $contentParameters, $subject);
			$body = str_replace(array_keys($contentParameters), $contentParameters, $body);
		}
		
		KalturaLog::info("Subject [$subject]");
		KalturaLog::info("Body [$body]");
		
		$this::$mailer->Subject = $subject;
		$this::$mailer->Body = $body;
	
		if(is_array($data->customHeaders) && count($data->customHeaders))
		{
			foreach($data->customHeaders as $customHeader)
			{
				/* @var $customHeader KalturaKeyValue */
				$this::$mailer->AddCustomHeader("{$customHeader->key}: {$customHeader->value}");
			}
		}
		
		try
		{
			$success = $this::$mailer->Send();
			if(!$success)
				throw new kTemporaryException("Sending mail failed: " . $this::$mailer->ErrorInfo);
		}
		catch(Exception $e)
		{
			throw new kTemporaryException("Sending mail failed with exception: " . $e->getMessage(), $e->getCode());	
		}
			
		return true;
	}
}
