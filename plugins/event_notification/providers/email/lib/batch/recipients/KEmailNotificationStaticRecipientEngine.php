<?php
/**
 * Engine which retrieves a static list of email recipients
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KEmailNotificationStaticRecipientEngine extends KEmailNotificationRecipientEngine
{
	/* (non-PHPdoc)
	 * @see KEmailNotificationRecipientEngine::getRecipients()
	 */
	function getRecipients(array $contentParameters) 
	{
		$recipients = array();
		foreach ($this->recipientJobData->emailRecipients as $emailRecipient)
		{
			/* var $emailRecipient KalturaKeyValue */
			$email = $emailRecipient->key;
			$name = $emailRecipient->value;
			if(is_array($contentParameters) && count($contentParameters))
			{
				$email = str_replace(array_keys($contentParameters), $contentParameters, $email);
				$name = str_replace(array_keys($contentParameters), $contentParameters, $name);
			}
			$recipients[$email] = $name;
		}
		
		return $recipients;
	}
}