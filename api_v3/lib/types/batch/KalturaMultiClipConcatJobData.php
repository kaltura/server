<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaMultiClipConcatJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $destEntryId;

	/**
	 * @var string
	 */
	public $multiTempEntryId;

	/**
	 * @var int
	 */
	public $partnerId;

	/**
	 * @var int
	 */
	public $priority;

	/**
	 * @var KalturaOperationResourceArray
	 */
	public $operationResources;


	private static $map_between_objects = array
	(
		'destEntryId',
		'multiTempEntryId',
		'partnerId',
		'priority',
		'operationResources'
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
		{
			$dbData = new kMultiClipConcatJobData();
		}

		return parent::toObject($dbData, $props_to_skip);
	}
}