<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
abstract class BusinessProcessNotificationTemplate extends EventNotificationTemplate
{
	const CUSTOM_DATA_SERVER_ID = 'serverId';
	const CUSTOM_DATA_PROCESS_ID = 'processId';
	
	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::getJobData()
	 */
	public function getJobData(kScope $scope = null)
	{
		$jobData = new kBusinessProcessNotificationDispatchJobData();
		$jobData->setTemplateId($this->getId());
		$jobData->setServerId($this->getServerId());
		
		$contentParametersValues = array();
		$contentParameters = $this->getContentParameters();
		foreach($contentParameters as $contentParameter)
		{
			/* @var $contentParameter kEventNotificationParameter */
			$value = $contentParameter->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
				
			$contentParametersValues[$contentParameter->getKey()] = $value->getValue();
		}
		$jobData->setContentParameters($contentParametersValues);
		
		if($scope instanceof kEventScope)
		{
			$object = $scope->getObject();
			$jobData->setObject($object);
		}
		
		return $jobData;
	}
	
	public function getCaseId(BaseObject $object)
	{
		if(method_exists($object, 'getFromCustomData'))
		{
			$caseId = $object->getFromCustomData($this->getProcessId(), 'businessProcessCaseId_' . $this->getServerId());
			if(!$caseId)
			{
				KalturaLog::err('Object [' . get_class($object) . '][' . $object->getPrimaryKey() . '] case id not found in custom-data');
			}
			return $caseId;
		}
		KalturaLog::err('Object [' . get_class($object) . '] does not support custom-data');
		return null;
	}
	
	public function setCaseId(BaseObject $object, $caseId)
	{
		if(method_exists($object, 'putInCustomData'))
		{
			$object->putInCustomData($this->getProcessId(), $caseId, 'businessProcessCaseId_' . $this->getServerId());
			$object->save();
		}
	}
	
	public function getServerId()									{return $this->getFromCustomData(self::CUSTOM_DATA_SERVER_ID);}
	public function getProcessId()									{return $this->getFromCustomData(self::CUSTOM_DATA_PROCESS_ID);}
	
	public function setServerId($v)									{return $this->putInCustomData(self::CUSTOM_DATA_SERVER_ID, $v);}
	public function setProcessId($v)								{return $this->putInCustomData(self::CUSTOM_DATA_PROCESS_ID, $v);}
}
