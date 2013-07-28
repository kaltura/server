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
}