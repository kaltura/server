<?php
/**
 * @package plugins.pushNotification
 * @subpackage api.objects
*/
class KalturaPushNotificationTemplate extends KalturaEventNotificationTemplate
{
	/**
	 * Define the content dynamic parameters
	 * @var KalturaPushEventNotificationParameterArray
	 * @requiresPermission update
	 */
	public $queueNameParameters;
	
	/**
	 * Define the content dynamic parameters
	 * @var KalturaPushEventNotificationParameterArray
	 * @requiresPermission update
	 */
	public $queueKeyParameters;
	
    /**
     * Kaltura API object type
     * @var string
     */
    public $apiObjectType;
    
    /**
     * Kaltura Object format
     * @var KalturaResponseType
     */    
    public $objectFormat;
    
    /**
     * Kaltura response-profile id
     * @var int
     */    
    public $responseProfileId;
    

    private static $map_between_objects = array('apiObjectType', 'objectFormat', 'responseProfileId', 'queueNameParameters', 'queueKeyParameters');
    
    public function __construct()
    {
        $this->type = PushNotificationPlugin::getApiValue(PushNotificationTemplateType::PUSH);
    }
    
    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }    

    /* (non-PHPdoc)
     * @see KalturaObject::validateForUpdate()
     */
    public function validateForUpdate($sourceObject, $propertiesToSkip = array())
    {
        $propertiesToSkip[] = 'type';
        return parent::validateForUpdate($sourceObject, $propertiesToSkip);
    }
    
    /* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
    public function toObject($dbObject = null, $propertiesToSkip = array())
    {
        if(is_null($dbObject))
            $dbObject = new PushNotificationTemplate();
        	
        return parent::toObject($dbObject, $propertiesToSkip);
    }
}