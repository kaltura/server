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
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig;
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaClient $client
	 */
	public function __construct(KSchedularTaskConfig $taskConfig, KalturaClient $client)
	{
		$this->client = $client;
		$this->taskConfig = $taskConfig;
	}
	
	/**
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate
	 * @param KalturaEventNotificationDispatchJobData $data
	 */
	abstract public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData $data);
}
