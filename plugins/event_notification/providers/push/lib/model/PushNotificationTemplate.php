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
        
        // check if queue exists
        $contentParameters = $this->getContentParameters();
        $queueKey = $this->getQueueKey($contentParameters, null, $scope);
        $queueProvider = QueueProvider::getInstance();
        if (!$queueProvider->exists($queueKey))
        {
            KalturaLog::info("Queue [$queueKey] doesn't exist.");
            return false;
        }
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
    
    public function getQueueKey($contentParameters, $partnerId = null, kScope $scope = null)
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
        
        $contentParamsHash = md5($partnerId . '_' . implode( '_' , array_values($contentParametersValues) ) );
        // prepare queue key to return
        return 'pn_' . $templateId . '_' . $contentParamsHash;
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
        if (!$scope || !($scope instanceof kEventScope))
        {
            KalturaLog::err('Failed to dispatch due to incorrect scope [' .$scope . ']');
            return;
        }
        
        $contentParameters = $this->getContentParameters();
        $queueKey = $this->getQueueKey($contentParameters, null, $scope);
        
        // get instance of activated queue proivder and send message
        $queueProvider = QueueProvider::getInstance();
        $queueProvider->send($queueKey, $this->getMessage($scope));
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