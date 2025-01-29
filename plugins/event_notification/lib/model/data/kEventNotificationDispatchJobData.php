<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventNotificationDispatchJobData extends kJobData
{
	/**
	 * @var int
	 */
	protected $templateId;
	
	/**
	 * @var int
	 */
	protected $jobId;

	/**
	 * @var KalturaEventNotificationDelayedCondition
	 */
	protected $eventDelayedCondition;
	
	/**
	 * Define the content dynamic parameters
	 * 
	 * @var array<key,value>
	 */
	protected $contentParameters;

	/**
	 * @return EventNotificationDelayedCondition $eventDelayedCondition
	 */
	public function getEventDelayedCondition()
	{
		return $this->eventDelayedCondition;
	}

	/**
	 * @param EventNotificationDelayedCondition $eventDelayedCondition
	 */
	public function setEventDelayedCondition($eventDelayedCondition)
	{
		$this->eventDelayedCondition = $eventDelayedCondition;
	}

	/**
	 * @return int $jobId
	 */
	public function getJobId() 
	{
		return $this->jobId;
	}

	/**
	 * @param int $jobId
	 */
	public function setJobId($jobId) 
	{
		$this->jobId = $jobId;
	}

	/**
	 * @return int $templateId
	 */
	public function getTemplateId() 
	{
		return $this->templateId;
	}

	/**
	 * @param int $templateId
	 */
	public function setTemplateId($templateId) 
	{
		$this->templateId = $templateId;
	}

	/**
	 * @return array<key,value> $contentParameters
	 */
	public function getContentParameters()
	{
		return $this->contentParameters;
	}

	/**
	 * @param array<key,value> $contentParameters
	 */
	public function setContentParameters(array $contentParameters)
	{
		$this->contentParameters = $contentParameters;
	}
}
