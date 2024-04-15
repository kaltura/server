<?php

/**
 * Represents the Bulk upload job data for filter bulk upload
 * @package plugins.bulkUploadFilter
 * @subpackage api.objects
 */
class KalturaBulkUploadFilterJobData extends KalturaBulkUploadJobData
{	
	/**
	 * Filter for extracting the objects list to upload 
	 * @var KalturaFilter
	 */
	public $filter;

	/**
	 * Template object for new object creation
	 * @var KalturaObject
	 */
	public $templateObject;
	
	/**
	 * 
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	(
		"filter",
	);

	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kBulkUploadFilterJobData();
		
		$templateObjectClass = $this->templateObject ? get_class($this->templateObject) : null;
		switch ($templateObjectClass)
	    {
	        case 'KalturaCategoryEntry':
	           	$dbData->setTemplateObject(new categoryEntry());
	           	$this->templateObject->toObject($dbData->getTemplateObject());
	            break;
	        default:
	            break;
	    }
	    
		return parent::toObject($dbData);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
	    parent::doFromObject($source_object, $responseProfile);
	    
	    /* @var $source_object kBulkUploadFilterJobData */
	    $this->filter = null;
	    $objectFilter = $source_object->getFilter();
	    $objectFilterClass = (!is_null($objectFilter)) ? get_class($objectFilter) : null;
	    switch ($objectFilterClass)
	    {
	        case 'categoryEntryFilter':
	            $this->filter = new KalturaCategoryEntryFilter();
	            break;
	        case 'entryFilter':
	            $this->filter = new KalturaBaseEntryFilter();
	            break;
		case 'UserEntryFilter':
	  	    $this->filter = new KalturaUserEntryFilter();
		    break;
	        default:
	            break;
	    }
	    
	    if ($this->filter)
	    {
	        $this->filter->fromObject($source_object->getFilter());
	    }
	    
	    $this->templateObject = null;
	    $templateObjectClass = $source_object->getTemplateObject() ? get_class($source_object->getTemplateObject()) : null;
	    switch ($templateObjectClass)
	    {
	        case 'categoryEntry':
	            $this->templateObject = new KalturaCategoryEntry();
	            break;
	        default:
	            break;
	    }
	    
	    if ($this->templateObject)
	    {
	        $this->templateObject->fromObject($source_object->getTemplateObject());
	    }       
	}
	
	public function toInsertableObject($object_to_fill = null , $props_to_skip = array())
	{
	    $dbObj = parent::toInsertableObject($object_to_fill, $props_to_skip);
	    
	    $this->setType();
	    
	    return $dbObj;
	}
	
	public function setType ()
	{
	    $this->type = kPluginableEnumsManager::coreToApi("KalturaBulkUploadType", BulkUploadFilterPlugin::getApiValue(BulkUploadFilterType::FILTER));
	}
}
