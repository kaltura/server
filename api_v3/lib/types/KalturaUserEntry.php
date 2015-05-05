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
	 * @var int
	 */
	public $type;

	/*
	 * @var int
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
	 * @var myCustomData
	 */
	protected $m_custom_data = null;

	/**
	 * Function returns KalturaUserEntry sub-type according to protocol
	 * @var string $type
	 * @return KalturaUserEntry
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

}