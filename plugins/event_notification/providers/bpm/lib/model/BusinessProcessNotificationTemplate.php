<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
abstract class BusinessProcessNotificationTemplate extends BatchEventNotificationTemplate
{
	const CUSTOM_DATA_SERVER_ID = 'serverId';
	const CUSTOM_DATA_PROCESS_ID = 'processId';
	const CUSTOM_DATA_MAIN_OBJECT_CODE = 'mainObjectCode';
	
	
	/* (non-PHPdoc)
	 * @see BatchEventNotificationTemplate::getJobData()
	 */
	public function getJobData(kScope $scope = null)
	{
		$jobData = new kBusinessProcessNotificationDispatchJobData();
		$jobData->setTemplateId($this->getId());
		$jobData->setServerId($this->getServerId());
		$jobData->setContentParameters($this->getParameters($scope));
		
		if($scope instanceof kEventScope)
		{
			$object = $scope->getObject();
			$jobData->setObject($object);
		}
		
		return $jobData;
	}

	protected function getParameters(kScope $scope)
	{
		$parametersValues = array();
		$contentParameters = $this->getContentParameters();
		foreach($contentParameters as $contentParameter)
		{
			/* @var $contentParameter kEventNotificationParameter */
			$value = $contentParameter->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
				
			$parametersValues[$contentParameter->getKey()] = $value->getValue();
		}
		$userParameters = $this->getUserParameters();
		foreach($userParameters as $userParameter)
		{
			/* @var $userParameter kEventNotificationParameter */
			$value = $userParameter->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
				
			$parametersValues[$userParameter->getKey()] = $value->getValue();
		}
		
		return $parametersValues;
	}

	protected function dispatchPerCase(kScope $scope, $eventNotificationType = null)
	{
		$jobData = $this->getJobData($scope);
		/* @var $jobData kBusinessProcessNotificationDispatchJobData */
		if(!$jobData->getObject())
		{
			return null;
		}
		
		$caseIds = $this->getCaseIds($jobData->getObject());
		$jobId = null;
		foreach($caseIds as $caseId)
		{
			$currentJobData = clone $jobData;
			$currentJobData->setCaseId($caseId);
			$jobId = $this->dispatchJob($scope, $currentJobData, $eventNotificationType);
		}
		return $jobId;
	}
	
	public static function getCaseTemplatesIds(BaseObject $object)
	{
		if(method_exists($object, 'getFromCustomData'))
		{
			$values = $object->getFromCustomData(null, 'businessProcessCases', array());
			if(!$values || !count($values))
			{
				return array();
			}
			$templatesIds = array();
			foreach($values as $cases)
			{
				foreach($cases as $case)
				{
					$templatesIds[] = $case['templateId'];
				}
			}
			return $templatesIds;
		}
		return array();
	}
	
	public function getCaseIds(BaseObject $object, $applyMainObject = true)
	{
		if($applyMainObject)
		{
			$object = $this->getMainObject($object);
		}
		
		if(method_exists($object, 'getFromCustomData'))
		{
			$values = $object->getFromCustomData($this->getServerId() . '_' . $this->getProcessId(), 'businessProcessCases', array());
			if(!$values || !count($values))
			{
				KalturaLog::info('Object [' . get_class($object) . '][' . $object->getPrimaryKey() . '] case id not found in custom-data');
			}
			$caseIds = array();
			foreach($values as $value)
			{
				$caseIds[] = $value['caseId'];
			}
			return $caseIds;
		}
		KalturaLog::err('Object [' . get_class($object) . '] does not support custom-data');
		return array();
	}
	
	public function addCaseId(BaseObject $object, $caseId, $processId = null)
	{
		$objectToAdd = $this->getMainObject($object);
		if(method_exists($objectToAdd, 'putInCustomData'))
		{
			if(is_null($processId))
			{
				$processId = $this->getProcessId();
			}
			
			$values = $this->getCaseIds($object);
			$values[] = array(
				'caseId' => $caseId,
				'processId' => $processId,
				'templateId' => $this->getId(),
			);
			$objectToAdd->putInCustomData($this->getServerId() . '_' . $processId, $values, 'businessProcessCases');
			$objectToAdd->save();
		}
	}

	protected function getMainObject(BaseObject $object)
	{
		$code = $this->getMainObjectCode();
		if(is_null($code))
		{
			return $object;
		}
		
		$object = eval("return $code;");
		if($object && $object instanceof BaseObject)
		{
			return $object;
		}
	
		return null;
	}
	
	public function getServerId()									{return $this->getFromCustomData(self::CUSTOM_DATA_SERVER_ID);}
	public function getProcessId()									{return $this->getFromCustomData(self::CUSTOM_DATA_PROCESS_ID);}
	public function getMainObjectCode()								{return $this->getFromCustomData(self::CUSTOM_DATA_MAIN_OBJECT_CODE);}
	
	public function setServerId($v)									{return $this->putInCustomData(self::CUSTOM_DATA_SERVER_ID, $v);}
	public function setProcessId($v)								{return $this->putInCustomData(self::CUSTOM_DATA_PROCESS_ID, $v);}
	public function setMainObjectCode($v)							{return $this->putInCustomData(self::CUSTOM_DATA_MAIN_OBJECT_CODE, $v);}
}
