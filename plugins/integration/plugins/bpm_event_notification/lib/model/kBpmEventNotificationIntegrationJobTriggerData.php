<?php
/**
 * @package plugins.bpmEventNotificationIntegration
 * @subpackage model.data
 */
class kBpmEventNotificationIntegrationJobTriggerData extends kIntegrationJobTriggerData
{
	/**
	 * KalturaBusinessProcessNotificationTemplate id
	 * @var int
	 */
	private $templateId;
	
	/**
	 * Execution unique id
	 * @var string
	 */
	private $caseId;
	
	/**
	 * @return the $templateId
	 */
	public function getTemplateId()
	{
		return $this->templateId;
	}

	/**
	 * @return the $caseId
	 */
	public function getCaseId()
	{
		return $this->caseId;
	}

	/**
	 * @param int $templateId
	 */
	public function setTemplateId($templateId)
	{
		$this->templateId = $templateId;
	}

	/**
	 * @param string $caseId
	 */
	public function setCaseId($caseId)
	{
		$this->caseId = $caseId;
	}
}