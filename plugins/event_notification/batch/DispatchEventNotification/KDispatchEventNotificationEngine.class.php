<?php
/**
 * @package plugins.eventNotification
 * @subpackage Scheduler
 */
abstract class KDispatchEventNotificationEngine
{	
	
	/**
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @param KalturaEventNotificationDispatchJobData $data
	 */
	abstract public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData &$data);
}
