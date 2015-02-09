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
	
	public function fromObject($source_object, IResponseProfile $responseProfile = null)
	{
		if ($source_object instanceof uiConf)
		{
			if ($source_object->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK)
				$this->isPublic = true;
			else
				$this->isPublic = false;
		}
		
		return parent::fromObject($source_object, $responseProfile);
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