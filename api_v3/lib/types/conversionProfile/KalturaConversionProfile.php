<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConversionProfile extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Conversion Profile
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Conversion Profile
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * The description of the Conversion Profile
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * Creation date as Unix timestamp (In seconds) 
	 * 
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	/**
	 * List of included flavor ids (comma separated)
	 * 
	 * @var string
	 */
	public $flavorParamsIds;
	
	/**
	 * True if this Conversion Profile is the default
	 *  
	 * @var KalturaNullableBoolean
	 */
	public $isDefault;
	
	/**
	 * Cropping dimensions
	 * 
	 * @var KalturaCropDimensions
	 */
	public $cropDimensions;
	
	/**
	 * Clipping start position (in miliseconds)
	 * 
	 * @var int
	 */
	public $clipStart;
	
	/**
	 * Clipping duration (in miliseconds)
	 * 
	 * @var int
	 */
	public $clipDuration;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"name",
		"description",
		"createdAt",
		"isDefault",
		"clipStart",
		"clipDuration",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		$this->cropDimensions = new KalturaCropDimensions();
		$this->cropDimensions->fromObject($sourceObject);
	}
	
	public function toObject($objectToFill = null , $propsToSkip = array())
	{
		parent::toObject($objectToFill, $propsToSkip);
		
		if ($this->cropDimensions !== null)
		{
			$this->cropDimensions->toObject($objectToFill);
		}
		
		if ($this->isDefault === KalturaNullableBoolean::NULL_VALUE) // like null
			$this->isDefault = null;
	}
	
	public function toUpdatableObject($objectToFill , $propsToSkip = array())
	{
		parent::toUpdatableObject($objectToFill, $propsToSkip);
		
		if ($this->cropDimensions !== null)
		{
			$this->cropDimensions->toUpdatableObject($objectToFill);
		}
	}
	
	public function validateFlavorParamsIds()
	{
		$flavorParamsIds = $this->getFlavorParamsAsArray();
		assetParamsPeer::resetInstanceCriteriaFilter();
		$flavorParams = assetParamsPeer::retrieveByPKs($flavorParamsIds);
		
		$sourceFound = false;
		$indexedFlavorParams = array();
		foreach($flavorParams as $flavorParamsItem)
		{
			if($flavorParamsItem->hasTag(flavorParams::TAG_SOURCE))
			{
				if($sourceFound)
					throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_SOURCE_DUPLICATE);
					
				$sourceFound = true;
			}
			$indexedFlavorParams[$flavorParamsItem->getId()] = $flavorParamsItem;
		}
			
		$foundFlavorParams = array();
		foreach($flavorParamsIds as $id)
		{
			if(!isset($indexedFlavorParams[$id]))
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
				
			if(in_array($id, $foundFlavorParams))
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_DUPLICATE, $id);
				
			$foundFlavorParams[] = $id;
		}
	}
	
	public function getFlavorParamsAsArray()
	{
		return explode(",", $this->flavorParamsIds);
	}
	
	public function loadFlavorParamsIds(conversionProfile2 $conversionProfile, $con = null)
	{
		$flavorParams = $conversionProfile->getflavorParamsConversionProfilesJoinflavorParams(null, $con);
		$flavorParamIds = array();
		foreach($flavorParams as $flavorParam)
		{
			$flavorParamIds[] = $flavorParam->getFlavorParamsId();
		}
		$this->flavorParamsIds = implode(",", $flavorParamIds);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}