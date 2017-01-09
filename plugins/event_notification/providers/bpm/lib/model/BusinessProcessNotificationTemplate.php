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
		//Dtermine object type
		//Get all templates
		$criteria = new Criteria();
		$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
		
		$bpmProcessTypes = array ();
		$bpmProcessTypes[] = BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue (BusinessProcessNotificationTemplateType::BPM_START);
		$bpmProcessTypes[] = BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue (BusinessProcessNotificationTemplateType::BPM_ABORT);
		$bpmProcessTypes[] = BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue (BusinessProcessNotificationTemplateType::BPM_SIGNAL);
		$criteria->add(EventNotificationTemplatePeer::TYPE, $bpmProcessTypes, Criteria::IN);
		
		$templates = EventNotificationTemplatePeer::doSelect($criteria);
		
		$eventObjectType = null;
		foreach ($templates as $template)
		{
			$templateObjectClassName = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $template->getObjectType());
			if(!strcmp(get_class($object), $templateObjectClassName) || is_subclass_of(get_class ($object), $templateObjectClassName))
			{
				$eventObjectType = $template->getObjectType ();
				break;
			}
		}
		
		if (!$eventObjectType)
		{
			KalturaLog::info ("There are currently no Business Process Templates for objects of type [" . get_class($object) . "]");
			return array ();
		}
		
		$cases = BusinessProcessCasePeer::retrieveCasesByObjectIdObjecType($object->getId(), $eventObjectType, kCurrentContext::getCurrentPartnerId());
		
		$templatesIds = array();
		foreach ($cases as $case)
		{
			/* @var $case BusinessProcessCase */
			$templatesIds[] = $case->getTemplateId();
		}
		
		return $templatesIds;
	}

	public function getCaseValues(BaseObject $object, $applyMainObject = true, $processId = null)
	{
		if($applyMainObject)
		{
			$object = $this->getMainObject($object);
		}
		
		if(is_null($processId))
		{
			$processId = $this->getProcessId();
		}
		
		$results = BusinessProcessCasePeer::retrieveCasesByObjectIdObjectTypeProcessIdServerId($object->getId(), $this->getObjectType(), $this->getServerId(), $processId, $this->getPartnerId());
		if(!$results || !count($results))
		{
			KalturaLog::info('Object [' . get_class($object) . '][' . $object->getPrimaryKey() . '] case id not found in custom-data');
		}
		else
		{
			KalturaLog::debug('Case values for [' . $this->getServerId() . '_' . $processId . ']: ' . print_r($results, true));
		}

		return $results;
	}

	public function getCaseIds(BaseObject $object, $applyMainObject = true)
	{
		$values = $this->getCaseValues($object, $applyMainObject);
		$caseIds = array();
		foreach($values as $value)
		{
			/* @var $value BusinessProcessCase */
			$caseIds[] = $value->getCaseId();
		}
		
		return $caseIds;
	}
	
	public function addCaseId(BaseObject $object, $caseId, $processId = null)
	{
		$objectToAdd = $this->getMainObject($object);
		
		if(is_null($processId))
		{
			$processId = $this->getProcessId();
		}
		
		$businessProcessCase = new BusinessProcessCase();
		$businessProcessCase->setPartnerId($this->getPartnerId());
		$businessProcessCase->setCaseId($caseId);
		$businessProcessCase->setProcessId($processId);
		$businessProcessCase->setTemplateId($this->getId());
		$businessProcessCase->setServerId($this->getServerId());
		$businessProcessCase->setObjectId($object->getId());
		$businessProcessCase->setObjectType($this->getObjectType());
		
		$businessProcessCase->save();
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
