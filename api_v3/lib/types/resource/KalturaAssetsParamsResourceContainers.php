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
    	
		$dc = null;
    	foreach($this->resources as $resource)
    	{
    		$resource->validateEntry($dbEntry);
    	
    		if(!($resource instanceof KalturaDataCenterContentResource))
    			continue;
    			
    		$theDc = $resource->getDc();
    		if(is_null($theDc))
    			continue;
    			
    		if(is_null($dc))
    		{
    			$dc = $theDc;
    		}
    		elseif($dc != $theDc)
    		{
				throw new KalturaAPIException(KalturaErrors::RESOURCES_MULTIPLE_DATA_CENTERS);
    		}
    	}
    	
    	if(!is_null($dc) && $dc != kDataCenterMgr::getCurrentDcId())
    	{
    		$remoteHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($dc);
    		kFileUtils::dumpApiRequest($remoteHost);
    	}
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