<?php
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
	 */
	public $status;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $optionalFlavorParamsIds;

	/**
	 * @var string
	 */
	public $requiredFlavorParamsIds;

	/**
	 * @var string
	 */
	public $optionalThumbDimensions;

	/**
	 * @var string
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
		'name',
		'optionalFlavorParamsIds',
		'requiredFlavorParamsIds',
		'optionalThumbDimensions',
		'requiredThumbDimensions',
		'editableFields',
		'mandatoryFields',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}