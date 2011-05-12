<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaUiConfAdmin extends KalturaUiConf
{
	/**
	 * @var bool
	 */
	public $isPublic;
	
	private static $map_between_objects = array(
		'isPublic'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function fromObject($source_object)
	{
		if ($source_object instanceof uiConf)
		{
			if ($source_object->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK)
				$this->isPublic = true;
			else
				$this->isPublic = false;
		}
		
		return parent::fromObject($source_object);
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array()) 
	{
		if ($object_to_fill instanceof uiConf)
		{
			if ($this->isPublic === true)
				$object_to_fill->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
			else
				$object_to_fill->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_NONE);
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}