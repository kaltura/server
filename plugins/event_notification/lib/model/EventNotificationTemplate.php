<?php


/**
 * Skeleton subclass for representing a row from the 'event_notification_template' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.eventNotification
 * @subpackage model
 */
abstract class EventNotificationTemplate extends BaseEventNotificationTemplate implements IBaseObject
{
	const CUSTOM_DATA_EVENT_CONDITIONS = 'eventConditions';
	const CUSTOM_DATA_CONTENT_PARAMETERS = 'contentParameters';
	const CUSTOM_DATA_USER_PARAMETERS = 'userParameters';
	const CUSTOM_DATA_MANUAL_DISPATCH_ENABLED = 'manualDispatchEnabled';
	const CUSTOM_DATA_AUTOMATIC_DISPATCH_ENABLED = 'automaticDispatchEnabled';
	const CUSTOM_DATA_URL_ENCODE = 'urlEncode';

	/**
	 * Dispatch the event notification
	 * @param kScope $scope
	 */ 
	abstract public function dispatch(kScope $scope);

	public function getEventConditions()									{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_CONDITIONS);}
	public function getContentParameters()									{return $this->getFromCustomData(self::CUSTOM_DATA_CONTENT_PARAMETERS, null, array());}
	public function getUserParameters()										{return $this->getFromCustomData(self::CUSTOM_DATA_USER_PARAMETERS, null, array());}
	public function getManualDispatchEnabled()								{return $this->getFromCustomData(self::CUSTOM_DATA_MANUAL_DISPATCH_ENABLED);}
	public function getAutomaticDispatchEnabled()							{return $this->getFromCustomData(self::CUSTOM_DATA_AUTOMATIC_DISPATCH_ENABLED);}
	public function getUrlEncode()											{return $this->getFromCustomData(self::CUSTOM_DATA_URL_ENCODE, null, true);}
	
	public function setEventConditions(array $v)							{return $this->putInCustomData(self::CUSTOM_DATA_EVENT_CONDITIONS, $v);}
	public function setContentParameters(array $v)							{return $this->putInCustomData(self::CUSTOM_DATA_CONTENT_PARAMETERS, $v);}
	public function setUserParameters(array $v)								{return $this->putInCustomData(self::CUSTOM_DATA_USER_PARAMETERS, $v);}
	public function setManualDispatchEnabled($v)							{return $this->putInCustomData(self::CUSTOM_DATA_MANUAL_DISPATCH_ENABLED, $v);}
	public function setAutomaticDispatchEnabled($v)							{return $this->putInCustomData(self::CUSTOM_DATA_AUTOMATIC_DISPATCH_ENABLED, $v);}
	public function setUrlEncode($v)										{return $this->putInCustomData(self::CUSTOM_DATA_URL_ENCODE, $v);}
	
	public function getRequiredCopyTemplatePermissions ()
	{
		return $this->getFromCustomData('requiredCopyTemplatePermissions', null, array());
	}
	
	public function setRequiredCopyTemplatePermissions ($v)
	{
		if(!is_array($v))
			$v = array_map('trim', explode(',', $v));
			
		$this->putInCustomData('requiredCopyTemplatePermissions', $v);
	}
	public function getCacheInvalidationKeys()
	{
		return array("eventNotificationTemplate:id=".strtolower($this->getId()), "eventNotificationTemplate:partnerId=".strtolower($this->getPartnerId()));
	}
	
	public function getContentParametersKeyValueArray()
	{
		$contentParametersKeyValueArray = array();
	
		$contentParams = $this->getContentParameters();
		foreach ($contentParams as $contentParam)
		{
			$contentParametersKeyValueArray[$contentParam->getKey()] = $contentParam;
		}
	
		return $contentParametersKeyValueArray;
	}
	
	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->isNew())
		{
			$this->setUrlEncode(false);
		}
	
		return parent::preSave($con);
	}
	
	
	/**
	 * @param EventNotificationTemplate $notificationTemplate
	 * @param kEventScope $scope
	 * @return boolean
	 */
	public function fulfilled(kEventScope $scope)
	{
	    $eventConditions = $this->getEventConditions();
	    if(!$eventConditions || !count($eventConditions))
	        return true;
	
	    foreach($eventConditions as $eventCondition)
	    {
	        /* @var $eventCondition kCondition */
	        if(!$eventCondition->fulfilled($scope))
	            return false;
	    }
	
	    return true;
	}
	
	public function applyDynamicValues(&$scope)
	{
		$notificationParameters = $this->getContentParameters();
		foreach($notificationParameters as $notificationParameter)
		{
			/* @var $notificationParameter kEventNotificationParameter */
			if(!is_null($notificationParameter->getValue()))
				$scope->addDynamicValue($notificationParameter->getKey(), $notificationParameter->getValue());
		}
			
		$notificationParameters = $this->getUserParameters();
		foreach($notificationParameters as $notificationParameter)
		{
			/* @var $notificationParameter kEventNotificationParameter */
			if(!is_null($notificationParameter->getValue()))
				$scope->addDynamicValue($notificationParameter->getKey(), $notificationParameter->getValue());
		}
	}
	
} // EventNotificationTemplate
