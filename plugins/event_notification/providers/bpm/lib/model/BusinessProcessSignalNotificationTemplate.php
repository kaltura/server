<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessSignalNotificationTemplate extends BusinessProcessNotificationTemplate
{
	const CUSTOM_DATA_MESSAGE = 'message';
	const CUSTOM_DATA_EVENT_ID = 'eventId';
	
	public function __construct()
	{
		$this->setType(BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_SIGNAL));
		parent::__construct();
	}
	
	/* (non-PHPdoc)
	 * @see BusinessProcessNotificationTemplate::getJobData()
	 */
	public function getJobData(kScope $scope = null)
	{
		$jobData = parent::getJobData($scope);
	
		if($jobData->getObject())
		{
			$caseId = $this->getCaseId($jobData->getObject());
			$jobData->setCaseId($caseId);
		}
		
		return $jobData;
	}
	
	public function getMessage()									{return $this->getFromCustomData(self::CUSTOM_DATA_MESSAGE);}
	public function getEventId()									{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_ID);}
	
	public function setMessage($v)									{return $this->putInCustomData(self::CUSTOM_DATA_MESSAGE, $v);}
	public function setEventId($v)									{return $this->putInCustomData(self::CUSTOM_DATA_EVENT_ID, $v);}
}
