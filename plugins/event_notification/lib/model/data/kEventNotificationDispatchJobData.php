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
	private $templateId;
	
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