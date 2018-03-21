<?php
/**
 * Created by IntelliJ IDEA.
 * User: roie.beck
 * Date: 3/12/2018
 * Time: 11:20 AM
 */

class KalturaClipConcatJobData extends KalturaJobData
{

	/**$destEntryId
	 * @var string
	 */
	private $destEntryId;

	/**$tempEntryId
	 * @var string
	 */
	private $tempEntryId;

	/** $partnerId
	 * @var int
	 */
	private $partnerId;

	/** $priority
	 * @var int
	 */
	private $priority;


	/** clip operations
	 * @var array $operationAttributes
	 */
	private $operationAttributes;


	private static $map_between_objects = array
	(
		'destEntryId',
		'tempEntryId',
		'partnerId',
		'priority',
		'operationAttributes'
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