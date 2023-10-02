<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaClipConcatJobData extends KalturaJobData
{

	/**
	 * @var string
	 */
	public $destEntryId;

	/**
	 * @var string
	 */
	public $tempEntryId;

	/**
	 * @var string
	 */
	public $sourceEntryId;

	/**
	 * @var string
	 */
	public $importUrl;

	/**
	 * @var int
	 */
	public $partnerId;

	/**
	 * @var int
	 */
	public $priority;

	/**
	 * @var KalturaOperationAttributesArray
	 */
	public $operationAttributes;

	/**
	 * @var int
	 */
	public $resourceOrder;

	/**
	 * @var string
	 */
	public $conversionParams;


	private static $map_between_objects = array
	(
		'destEntryId',
		'tempEntryId',
		'partnerId',
		'priority',
		'operationAttributes',
		'sourceEntryId',
		'importUrl',
		'resourceOrder',
		'conversionParams'
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
			$dbData = new kClipConcatJobData();

		return parent::toObject($dbData, $props_to_skip);
	}
}