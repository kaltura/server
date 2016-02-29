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
	 * @var string
	 */
	private $businessProcessId;
	
	/**
	 * Execution unique id
	 * @var string
	 */
	private $caseId;
	
	/**
	 * @return string
	 */
	public function getBusinessProcessId()
	{
		return $this->businessProcessId;
	}

	/**
	 * @param string $businessProcessId
	 */
	public function setBusinessProcessId($businessProcessId)
	{
		$this->businessProcessId = $businessProcessId;
	}

	/**
	 * @return int
	 */
	public function getTemplateId()
	{
		return $this->templateId;
	}

	/**
	 * @return int
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