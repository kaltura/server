<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
*/
class PushNotificationTemplate extends EventNotificationTemplate
{
    const CUSTOM_DATA_API_OBJECT_TYPE = 'apiObjectType';
    const CUSTOM_DATA_OBJECT_FORMAT = 'objectFormat';
    const CUSTOM_DATA_RESPONSE_PROFILE_ID = 'responseProfileId';
    const CUSTOM_DATA_QUEUE_KEY_PARAMETERS = 'queueKeyParameters';
    const CUSTOM_DATA_QUEUE_NAME_PARAMETERS = 'queueNameParameters';
    const CONTENT_PARAMS_PERFIX = "s_cp_";
    const CONTENT_PARAMS_POSTFIX = "_e_cp";
    const QUEUE_PREFIX = "pn_";
    
    public function __construct()
    {
        $this->setType(PushNotificationPlugin::getPushNotificationTemplateTypeCoreValue(PushNotificationTemplateType::PUSH));
        parent::__construct();
    }

    public function fulfilled(kEventScope $scope)
    {
        if(!kCurrentContext::$serializeCallback)
            return false;
        
        if(!parent::fulfilled($scope))
            return false;
        
        return true;
    }
    
    public function setApiObjectType($value)
    {
        return $this->putInCustomData(self::CUSTOM_DATA_API_OBJECT_TYPE, $value);
    }
    
    public function getApiObjectType()
    {
        return $this->getFromCustomData(self::CUSTOM_DATA_API_OBJECT_TYPE);
    }
    
    public function setObjectFormat($value)
    {
        return $this->putInCustomData(self::CUSTOM_DATA_OBJECT_FORMAT, $value);
    }
    
    public function getObjectFormat()
    {
        return $this->getFromCustomData(self::CUSTOM_DATA_OBJECT_FORMAT);
    }    
    
    public function setResponseProfileId($value)
    {
        return $this->putInCustomData(self::CUSTOM_DATA_RESPONSE_PROFILE_ID, $value);
    }
    
    public function getResponseProfileId()
    {
        return $this->getFromCustomData(self::CUSTOM_DATA_RESPONSE_PROFILE_ID);
    }

    public function setQueueKeyParameters(array $v)
	{
    	return $this->putInCustomData(self::CUSTOM_DATA_QUEUE_KEY_PARAMETERS, $v);
	}
	
	public function getQueueKeyParameters($returnAsKeyValue = false)
	{
		$queueNameParams = $this->getQueueNameParameters();
		$queueKeyParams = array_merge($queueNameParams, $this->getFromCustomData(self::CUSTOM_DATA_QUEUE_KEY_PARAMETERS, null, array()));
		
		if(!$returnAsKeyValue || !count($queueKeyParams))
			return $queueKeyParams;
		
		return $this->getAsKeyValueArray($queueKeyParams);
	}
	
	public function setQueueNameParameters(array $v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_QUEUE_NAME_PARAMETERS, $v);
	}
	
	public function getQueueNameParameters($returnAsKeyValue = false)
	{
		$queueNameParams = $this->getFromCustomData(self::CUSTOM_DATA_QUEUE_NAME_PARAMETERS, null, array());
		if(!$returnAsKeyValue || !count($queueNameParams))
			return $queueNameParams;
		
		return $this->getAsKeyValueArray($queueNameParams);
	}
	
	private function getAsKeyValueArray($params)
	{
		$keyValueArray = array();
		
		foreach ($params as $param)
		{
			$keyValueArray[$param->getKey()] = $param;
		}
		
		return $keyValueArray;
	}
    
    public function getQueueKey($contentParameters, $partnerId = null, kScope $scope = null, $returnRaw = false)
    {
        $templateId = $this->getId();
        if ($scope)
            $partnerId = $scope->getPartnerId();
        
        // currently contentParams contains only one param (entryId), but for further support
        foreach ($contentParameters as $contentParameter)
        {        	
            /* @var $contentParameter kEventNotificationParameter */
            $value = $contentParameter->getValue();
            if (($value instanceof kStringField) && ($scope) )
                $value->setScope($scope);
        
            $key = $contentParameter->getKey();
            $contentParametersValues[$key] = $value->getValue();
        }
        // sort array according to created keys
        ksort($contentParametersValues);
        
        $queueContentParams = $partnerId . '_' . implode( '_' , array_values($contentParametersValues));
        $queueKey = registerNotificationPostProcessor::QUEUE_PREFIX . $templateId . "_";
        if($returnRaw)
        	return $queueKey . registerNotificationPostProcessor::CONTENT_PARAMS_PREFIX . $queueContentParams . registerNotificationPostProcessor::CONTENT_PARAMS_POSTFIX;
        else
        	return $queueKey . md5($queueContentParams);
    }
    
    public function getQueueName($queueNameParams, $partnerId = null, kScope $scope = null)
    {
		return $this->getQueueKey($queueNameParams, $partnerId, $scope);
    }
    
    protected function getMessage(kScope $scope)
    {
        if ($scope instanceof kEventScope)
        {
            $object = $scope->getObject();
        }

        // prepare vars as configured by user in admin console
        $objectType = $this->getApiObjectType();
        $format = $this->getObjectFormat();
        $responseProfile = null;
        
        if($this->getResponseProfileId())
        {
        	$responseProfile = ResponseProfilePeer::retrieveByPK($this->getResponseProfileId());
        }
        
        return call_user_func(kCurrentContext::$serializeCallback, $object, $objectType, $format, $responseProfile);
    }
    
    public function dispatch(kScope $scope) 
    {
    	KalturaLog::debug("Dispatching event notification with name [{$this->getName()}] systemName [{$this->getSystemName()}]");
        if (!$scope || !($scope instanceof kEventScope))
        {
            KalturaLog::err('Failed to dispatch due to incorrect scope [' .$scope . ']');
            return;
        }
        
        $queueNameParameters = $this->getQueueNameParameters();
        $queueKeyParameters = $this->getQueueKeyParameters();
        $queueName = $this->getQueueName($queueNameParameters, null, $scope);
        $queueKey = $this->getQueueKey($queueKeyParameters, null, $scope);
        $message = $this->getMessage($scope);
        $time = time();

        $msg = json_encode(array(
        		"data" 		=> $message, 
        		"queueKey" 	=> $queueKey, 
        		"queueName"	=> $queueName,
        		"msgId"		=> md5("$message $time"), 
        		"msgTime"	=> $time,
        		"command"	=> null 
        ));
        // get instance of activated queue proivder and send message
        $queueProvider = QueueProvider::getInstance();

        $queueProvider->send('', $msg);
    }
    
    public function create($queueKey)
    {
        // get instance of activated queue proivder and create queue with given name
        $queueProvider = QueueProvider::getInstance();
        $queueProvider->create($queueKey);
    }
    
    public function exists($queueKey)
    {
        // get instance of activated queue proivder and check whether given queue exists
        $queueProvider = QueueProvider::getInstance();
        return $queueProvider->exists($queueKey);
    }
    
    public function applyDynamicValues(&$scope)
    {
    	parent::applyDynamicValues($scope);
    	
    	$notificationParameters = $this->getQueueKeyParameters();
    	foreach($notificationParameters as $notificationParameter)
    	{
    		/* @var $notificationParameter kEventNotificationParameter */
    		if(!is_null($notificationParameter->getValue()))
    			$scope->addDynamicValue($notificationParameter->getKey(), $notificationParameter->getValue());
    	}
    }
}