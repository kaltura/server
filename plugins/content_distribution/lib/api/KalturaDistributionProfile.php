<?php
/**
 * @abstract
 */
class KalturaDistributionProfile extends KalturaObject implements IFilterable
{
	/**
	 * Auto generated unique id
	 * 
	 * @readonly
	 * @var int
	 * @filter eq,in
	 */
	public $id;

	/**
	 * Profile creation date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Profile last update date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @readonly
	 * @var int
	 */
	public $partnerId;

	/**
	 * @insertonly
	 * @var KalturaDistributionProviderType
	 */
	public $providerType;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var KalturaDistributionProfileStatus
	 */
	public $status;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $submitEnabled;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $updateEnabled;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $deleteEnabled;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $reportEnabled;

	/**
	 * Comma separated flavor params ids that should be auto converted
	 * @var string
	 */
	public $autoCreateFlavors;

	/**
	 * Comma separated thumbnail params ids that should be auto generated
	 * @var string
	 */
	public $autoCreateThumb;

	/**
	 * Comma separated flavor params ids that should be submitted if ready
	 * @var string
	 */
	public $optionalFlavorParamsIds;

	/**
	 * Comma separated flavor params ids that required to be readt before submission
	 * @var string
	 */
	public $requiredFlavorParamsIds;

	/**
	 * Thumbnail dimensions that should be submitted if ready
	 * @var KalturaDistributionThumbDimensionsArray
	 */
	public $optionalThumbDimensions;

	/**
	 * Thumbnail dimensions that required to be readt before submission
	 * @var KalturaDistributionThumbDimensionsArray
	 */
	public $requiredThumbDimensions;
	
	/**
	 * If entry distribution sunrise not specified that will be the default since entry creation time, in seconds
	 * @var int
	 */
	public $sunriseDefaultOffset;
	
	/**
	 * If entry distribution sunset not specified that will be the default since entry creation time, in seconds
	 * @var int
	 */
	public $sunsetDefaultOffset;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		'id',
		'createdAt',
		'updatedAt',
		'partnerId',
		'providerType',
		'name',
		'status',
		'submitEnabled',
		'updateEnabled',
		'deleteEnabled',
		'reportEnabled',
		'autoCreateFlavors',
		'autoCreateThumb',
		'optionalFlavorParamsIds',
		'requiredFlavorParamsIds',
		'sunriseDefaultOffset',
		'sunsetDefaultOffset',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return null;
			
		parent::toObject($dbObject, $skip);
		
		$optionalThumbDimensionsArray = array();
		if($this->optionalThumbDimensions)
		{
			foreach($this->optionalThumbDimensions as $optionalThumbDimensions)
				$optionalThumbDimensionsArray[] = $optionalThumbDimensions->toObject();
		}
		$dbObject->setOptionalThumbDimensionsObjects($optionalThumbDimensionsArray);
		
		$requiredThumbDimensionsArray = array();	
		if($this->requiredThumbDimensions)
		{
			foreach($this->requiredThumbDimensions as $requiredThumbDimensions)
				$requiredThumbDimensionsArray[] = $requiredThumbDimensions->toObject();
		}
		$dbObject->setRequiredThumbDimensionsObjects($requiredThumbDimensionsArray);

		return $dbObject;
	}
	
	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
		
		$this->optionalThumbDimensions = KalturaDistributionThumbDimensionsArray::fromDbArray($sourceObject->getOptionalThumbDimensionsObjects());
		$this->requiredThumbDimensions = KalturaDistributionThumbDimensionsArray::fromDbArray($sourceObject->getRequiredThumbDimensionsObjects());
	}
	
	public function getExtraFilters()
	{
		return array(
		);
	}
	
	public function getFilterDocs()
	{
		return array(
		);
	}
}