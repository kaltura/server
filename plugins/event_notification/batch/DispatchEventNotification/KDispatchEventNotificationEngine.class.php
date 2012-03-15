<?php
/**
 * @package plugins.eventNotification
 * @subpackage Scheduler
 */
abstract class KDispatchEventNotificationEngine
{
	/**
	 * @var KalturaClient
	 */
	protected $client;
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaClient $client
	 */
	public function __construct(KSchedularTaskConfig $taskConfig, KalturaClient $client)
	{
		$this->client = $client;
	}
	
	/**
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @param KalturaEventNotificationDispatchJobData $data
	 */
	abstract public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData $data);
}
