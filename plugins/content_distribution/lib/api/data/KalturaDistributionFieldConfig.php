<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionFieldConfig extends KalturaObject
{

    /**
     * A value taken from a connector field enum which associates the current configuration to that connector field
     * Field enum class should be returned by the provider's getFieldEnumClass function.
     * @var string
     */
    public $fieldName;
    
    /**
     * A string that will be shown to the user as the field name in error messages related to the current field
     * @var string
     */
    public $userFriendlyFieldName;
    
    /**
     * 
     * An XSLT string that extracts the right value from the Kaltura entry MRSS XML.
     * The value of the current connector field will be the one that is returned from transforming the Kaltura entry MRSS XML using this XSLT string.
     * @var string
     */
    public $entryMrssXslt;
    
    /**
     * Is the field required to have a value for submission ?
     * @var KalturaDistributionFieldRequiredStatus
     */
    public $isRequired;

	/**
	 * @var string
	 */
	public $type;
    
    /**
     * Trigger distribution update when this field changes or not ?
     * @var bool
     */
    public $updateOnChange;
    
    /**
     * Entry column or metadata xpath that should trigger an update
     * 
     * @todo find a better solution for this
     * @var KalturaStringArray
     */
    public $updateParams;
    
    /**
     * Is this field config is the default for the distribution provider?
     * @var bool
     * @readonly
     */
    public $isDefault;
    
    /**
     * Is an error on this field going to trigger deletion of distributed content?
     * @var bool
     */
    public $triggerDeleteOnError;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'fieldName',
		'userFriendlyFieldName',
		'entryMrssXslt',
		'isRequired',
		'updateOnChange',
		'isDefault',
		'triggerDeleteOnError',
		'type',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DistributionFieldConfig();
			
		parent::toObject($dbObject, $skip);
		if ($this->updateParams && count($this->updateParams))
		{
			$updateParams = array();
			foreach ($this->updateParams as $updateParam) {
			    if (isset($updateParam->value)) {
				    $updateParams[] = $updateParam->value;
			    }
			}
			$dbObject->setUpdateParams($updateParams);
		}
					
		return $dbObject;
	}
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		
		if($this->shouldGet('updateParams', $responseProfile))
			$this->updateParams = KalturaStringArray::fromStringArray($source_object->getUpdateParams());
	}
	
}