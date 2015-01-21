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
	 * @see BatchEventNotificationTemplate::dispatch()
	 */
	public function dispatch(kScope $scope)
	{
		KalturaLog::debug("Dispatch id [" . $this->getId() . "]");
		return $this->dispatchPerCase($scope);
	}
	
	/* (non-PHPdoc)
	 * @see BusinessProcessNotificationTemplate::getCaseIds()
	 */
	public function getCaseIds(BaseObject $object)
	{
		if(method_exists($object, 'getFromCustomData'))
		{
			$values = $object->getFromCustomData(null, 'businessProcessCases');
			KalturaLog::debug("Values [" . print_r($values, true) . "]");
			if(!$values || !count($values))
			{
				KalturaLog::err('Object [' . get_class($object) . '][' . $object->getPrimaryKey() . '] case id not found in custom-data');
			}
			$caseIds = array();
			foreach($values as $cases)
			{
				foreach($cases as $case)
				{
					$caseIds[] = $case['caseId'];
				}
			}
			return $caseIds;
		}
		KalturaLog::err('Object [' . get_class($object) . '] does not support custom-data');
		return array();
	}
	
	public function getMessage()									{return $this->getFromCustomData(self::CUSTOM_DATA_MESSAGE);}
	public function getEventId()									{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_ID);}
	
	public function setMessage($v)									{return $this->putInCustomData(self::CUSTOM_DATA_MESSAGE, $v);}
	public function setEventId($v)									{return $this->putInCustomData(self::CUSTOM_DATA_EVENT_ID, $v);}
}
