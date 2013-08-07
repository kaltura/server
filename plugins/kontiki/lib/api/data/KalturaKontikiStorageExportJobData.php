<?php
/**
 * @package plugins.kontiki
 * @subpackage api.object
 */
 class KalturaKontikiStorageExportJobData extends KalturaStorageExportJobData
 {
	/**
	 * Holds the id of the exported asset
	 * @var string
	 */
 	public $flavorAssetId;
	
	/**
	 * Unique Kontiki MOID for the content uploaded to Kontiki
	 * @var string
	 */
	public $contentMoid;
	
	/**
	 * @var string
	 */
	public $serviceToken;
	
    
    private static $map_between_objects = array
    (
        'flavorAssetId',
        'serviceToken',
        'contentMoid',
    );
	
    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
    
 	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kKontikiStorageExportJobData();
			
		return parent::toObject($dbData);
	}
 }
