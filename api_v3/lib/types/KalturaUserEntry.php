<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaUserEntry extends KalturaObject
{

	/**
	 * unique auto-generated identifier
	 * @var int
	 * @readonly
	 */
	public $id;

	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var int
	 */
	public $userId;

	/**
	 * @var int
	 */
	public $partnerId;

	/**
	 * @var KalturaUserEntryType
	 * @readonly
	 */
	public $type;

	/**
	 * @var KalturaUserEntryStatus
	 * @readonly
	 */
	public $status;

	/**
	 * @var time
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var time
	 * @readonly
	 */
	public $updatedAt;


	private static $map_between_objects = array
	(
		"id",
		"entryId",
		"userId" => "KuserId",
		"partnerId",
		"type",
		"status",
		"createdAt",
		"updatedAt"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}


	/**
	 * Function returns KalturaUserEntry sub-type according to protocol
	 * @var string $type
	 * @return KalturaUserEntry
	 *
	 * MOVE FUNCTION to quiz plugin
	 */
	public static function getInstanceByType ($type)
	{
		$obj = null;
		switch ($type) {
			case KalturaUserEntryType::KALTURA_QUIZ_USER_ENTRY:
				$obj = new KalturaQuizUserEntry();
				break;
			default:
				KalturaLog::warning("The type '$type' is unknown");
				break;
		}
		return $obj;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
//	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
//	{
//		if(is_null($object_to_fill))
//			$object_to_fill = self::getInstanceByType($this->type);
//		return parent::toInsertableObject($object_to_fill, $props_to_skip);
//	}

}