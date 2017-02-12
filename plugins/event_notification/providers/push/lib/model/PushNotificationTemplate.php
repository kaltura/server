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
	
	public function getQueueKeyParameters()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_QUEUE_KEY_PARAMETERS, null, array());
	}
	
	public function getQueueKeyParametersKeyValueArray()
	{
		$queueKeyParametersKeyValueArray = array();
		
		$queueParams = $this->getQueueKeyParameters();
		foreach ($queueParams as $queueParam)
		{
			$queueKeyParametersKeyValueArray[$queueParam->getKey()] = $queueParam;
		}
		
		return $queueKeyParametersKeyValueArray;
	}
	
	public function getNotificationParameters()
	{
		return array_merge($this->getQueueKeyParameters(), parent::getNotificationParameters());
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
        if($returnRaw)
        	return 'pn_' . $templateId . '_md5s_' . $queueContentParams . "_md5e";
        else
        	return 'pn_' . $templateId . '_' . md5($queueContentParams);
    }
    
    public function getQueueName($contentParameters, $partnerId = null, kScope $scope = null)
    {
    	return $this->getQueueKey($contentParameters, $partnerId, $scope);
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
        
        $queueParameters = $this->getQueueKeyParameters();
        $contentParameters = $this->getContentParameters();
        $queueKey = $this->getQueueKey(array_merge($contentParameters, $queueParameters), null, $scope);
        $queueName = $this->getQueueName($contentParameters, null, $scope);
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
}