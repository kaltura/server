<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCopyJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be copied.
	 * @var KalturaFilter
	 */
	public $filter;
	
	/**
	 * Indicates the last id that copied, used when the batch crached, to re-run from the last crash point.
	 * @var int
	 */
	public $lastCopyId;
	
	/**
	 * Template object to overwrite attributes on the copied object
	 * @var KalturaObject
	 */
	public $templateObject;
	
	private static $map_between_objects = array
	(
		"lastCopyId" ,
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kCopyJobData();
			
		$dbData->setTemplateObject($this->templateObject->toInsertableObject());	
		
		return parent::toObject($dbData, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbData, IResponseProfile $responseProfile = null) 
	{
		/* @var $dbData kCopyJobData */
		$filter = $dbData->getFilter();
		$filterType = get_class($filter);
		switch($filterType)
		{
			case 'entryFilter':
				$this->filter = new KalturaBaseEntryFilter();
				$this->templateObject = new KalturaBaseEntry();
				break;
				
			case 'categoryFilter':
				$this->filter = new KalturaCategoryFilter();
				$this->templateObject = new KalturaCategory();
				break;
				
			case 'categoryEntryFilter':
				$this->filter = new KalturaCategoryEntryFilter();
				$this->templateObject = new KalturaCategoryEntry();
				break;
				
			case 'categoryKuserFilter':
				$this->filter = new KalturaCategoryUserFilter();
				$this->templateObject = new KalturaCategoryUser();
				break;
				
			default:
				$this->filter = KalturaPluginManager::loadObject('KalturaFilter', $filterType);
		}
		if($this->filter)
			$this->filter->fromObject($filter);
		
		if($this->templateObject)
			$this->templateObject->fromObject($dbData->getTemplateObject());
		
		return parent::fromObject($dbData, $responseProfile);
	}
}
