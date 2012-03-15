<?php
/**
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KDispatchEmailNotificationEngine extends KDispatchEventNotificationEngine
{
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::dispatch()
	 */
	public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData $data)
	{
		$this->sendEmail($eventNotificationTemplate, $data);
	}

	public function sendEmail(KalturaEmailNotificationTemplate $emailNotificationTemplate, KalturaEmailNotificationDispatchJobData $data)
	{
		// TODO
	}
}
