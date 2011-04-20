<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetsParamsResourceContainers extends KalturaResource 
{
	/**
	 * Array of resources associated with asset params ids
	 * @var KalturaAssetParamsResourceContainerArray
	 */
	public $resources;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('resources');
    	
    	foreach($this->resources as $resource)
    		$resource->validateEntry($dbEntry);
	}
	
	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);
		
    	foreach($this->resources as $resource)
    		$resource->entryHandled($dbEntry);
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kAssetsParamsResourceContainers();
			
		$resources = array();
		foreach($this->resources as $resource)
			$resources[] = $resource->toObject();
			
		$object_to_fill->setResources($resources);
		return $object_to_fill;
	}
}