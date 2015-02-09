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
		
		switch (get_class($this->templateObject))
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
	public function fromObject($source_object, IResponseProfile $responseProfile = null)
	{
	    parent::fromObject($source_object, $responseProfile);
	    
	    /* @var $source_object kBulkUploadFilterJobData */
	    $this->filter = null;
	    switch (get_class($source_object->getFilter()))
	    {
	        case 'categoryEntryFilter':
	            $this->filter = new KalturaCategoryEntryFilter();
	            break;
	        case 'entryFilter':
	            $this->filter = new KalturaBaseEntryFilter();
	            break;
	        default:
	            break;
	    }
	    
	    if ($this->filter)
	    {
	        KalturaLog::debug("Filter class was found: ". get_class($this->filter));
	        $this->filter->fromObject($source_object->getFilter());
	    }       
	    
	   	$this->templateObject = null;
	   	
	   	KalturaLog::debug("template object class: ". get_class($source_object->getTemplateObject()));
	    switch (get_class($source_object->getTemplateObject()))
	    {
	        case 'categoryEntry':
	            $this->templateObject = new KalturaCategoryEntry();
	            break;
	        default:
	            break;
	    }
	    
	    if ($this->templateObject)
	    {
	        KalturaLog::debug("Template object class was found: ". get_class($this->templateObject));
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