<?php
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
		$flavorParams = flavorParamsPeer::retrieveByPKs($flavorParamsIds);
		foreach($flavorParamsIds as $id)
		{
			$found = false;
			foreach($flavorParams as $flavorParam)
			{
				if ($flavorParam->getId() == $id)
					$found = true;
			}
			
			if (!$found)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
		}
	}
	
	public function getFlavorParamsAsArray()
	{
		return explode(",", $this->flavorParamsIds);
	}
	
	public function loadFlavorParamsIds(conversionProfile2 $conversionProfile)
	{
		$flavorParams = $conversionProfile->getflavorParamsConversionProfilesJoinflavorParams();
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