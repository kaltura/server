<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaGenericDistributionProvider extends KalturaDistributionProvider
{
	/**
	 * Auto generated
	 * 
	 * @readonly
	 * @var int
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Generic distribution provider creation date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Generic distribution provider last update date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @readonly
	 * @var int
	 * @filter eq,in
	 */
	public $partnerId;

	/**
	 * @var bool
	 * @filter eq,in
	 */
	public $isDefault;

	/**
	 * @var KalturaGenericDistributionProviderStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;

	/**
	 * @var string
	 */
	public $optionalFlavorParamsIds;

	/**
	 * @var string
	 */
	public $requiredFlavorParamsIds;

	/**
	 * @var KalturaDistributionThumbDimensionsArray
	 */
	public $optionalThumbDimensions;

	/**
	 * @var KalturaDistributionThumbDimensionsArray
	 */
	public $requiredThumbDimensions;

	/**
	 * @var string
	 */
	public $editableFields;

	/**
	 * @var string
	 */
	public $mandatoryFields;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'id',
		'createdAt',
		'updatedAt',
		'partnerId',
		'isDefault',
		'status',
		'optionalFlavorParamsIds',
		'requiredFlavorParamsIds',
		'editableFields',
		'mandatoryFields',
	);

	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new GenericDistributionProvider();
			
		$object = parent::toObject($object, $skip);
		
		$object->setScheduleUpdateEnabled($this->scheduleUpdateEnabled);
		$object->setDeleteInsteadUpdate($this->deleteInsteadUpdate);
		$object->setIntervalBeforeSunrise($this->intervalBeforeSunrise);
		$object->setIntervalBeforeSunset($this->intervalBeforeSunset);
		$object->setUpdateRequiredEntryFields(explode(',', $this->updateRequiredEntryFields));
		$object->setUpdateRequiredMetadataXpaths(explode(',', $this->updateRequiredMetadataXPaths));
		
		$thumbDimensions = array();
		if($this->optionalThumbDimensions)
		{
			foreach($this->optionalThumbDimensions as $thumbDimension)
				$thumbDimensions[] = $thumbDimension->toObject();
		}		
		$object->setOptionalThumbDimensionsObjects($thumbDimensions);
	
		
		$thumbDimensions = array();
		if($this->requiredThumbDimensions)
		{
			foreach($this->requiredThumbDimensions as $thumbDimension)
				$thumbDimensions[] = $thumbDimension->toObject();
		}
		$object->setRequiredThumbDimensionsObjects($thumbDimensions);
		
		return $object;		
	}

	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		$this->optionalThumbDimensions = KalturaDistributionThumbDimensionsArray::fromDbArray($sourceObject->getOptionalThumbDimensionsObjects());
		$this->requiredThumbDimensions = KalturaDistributionThumbDimensionsArray::fromDbArray($sourceObject->getRequiredThumbDimensionsObjects());
		
		$this->updateRequiredEntryFields = implode(',', $sourceObject->getUpdateRequiredEntryFields());
		$this->updateRequiredMetadataXPaths = implode(',', $sourceObject->getUpdateRequiredMetadataXPaths());
	}
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}