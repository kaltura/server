<?php
/**
 * @package plugins.kontiki
 * @subpackage api.object
 */
class KalturaKontikiStorageProfile extends KalturaStorageProfile
{
	
	/**
	 * @var string
	 */
	public $serviceToken;
	
	
	private static $map_between_objects = array
	(
		'serviceToken',
		
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	public function toObject ($dbObject = null, $props_to_skip = array())
	{
	    /* @var $dbObject KalturaStorageProfile */
		if (!$dbObject)
		{
			$dbObject = new KontikiStorageProfile();
		}
		
		$dbObject->setProtocol(KontikiPlugin::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI));
		$dbObject->setUrlManagerClass("kKontikiUrlManager");
        
		return parent::toObject($dbObject, $props_to_skip);
	}
    
    /* (non-PHPdoc)
     * @see KalturaObject::toInsertableObject()
     */
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
    {
        if(is_null($object_to_fill))
            $object_to_fill = new KontikiStorageProfile();
            
        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }
	
	/* (non-PHPdoc)
     * @see KalturaObject::validateForInsert()
     */
	public function validateForInsert ($propertiesToSkip = array())
	{
		if (!KontikiPlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::PERMISSION_NOT_FOUND, 'Kontiki permission not found for partner');
		}
	}

}
