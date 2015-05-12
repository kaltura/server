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

	/*
	 * @var string
	 */
	public $entryId;

	/*
	 * @var int
	 */
	public $userId;

	/*
	 * @var int
	 */
	public $partnerId;

	/*
	 * @var KalturaUserEntryType
	 */
	public $type;

	/*
	 * @var KalturaUserEntryStatus
	 * @readonly
	 */
	public $status;

	/*
	 * @var time
	 */
	public $createdAt;

	/*
	 * @var time
	 */
	public $updatedAt;

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
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new UserEntry();
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

}