<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaTrackEntry extends KalturaObject
{
	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var KalturaTrackEntryEventType
	 */
	public $trackEventType;

	/**
	 * @var string
	 */
	public $psVersion;

	/**
	 * @var string
	 */
	public $context;

	/**
	 * @var int
	 */
	public $partnerId;

	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var string
	 */
	public $hostName;

	/**
	 * @var string
	 */
	public $userId;

	/**
	 * @var string
	 */
	public $changedProperties;

	/**
	 * @var string
	 */
	public $paramStr1;

	/**
	 * @var string
	 */
	public $paramStr2;

	/**
	 * @var string
	 */
	public $paramStr3;

	/**
	 * @var string
	 */
	public $ks;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var time
	 */
	public $createdAt;

	/**
	 * @var time
	 */
	public $updatedAt;

	/**
	 * @var string
	 */
	public $userIp;

	private static $map_between_objects = array
	(
		"id",
		"trackEventType" => 'trackEventTypeId',
		"psVersion",
		"context",
		"partnerId",
		"entryId",
		"hostName",
		"userId" => "uid",
		"changedProperties",
		"paramStr1" => "param1Str",
		"paramStr2" => "param2Str",
		"paramStr3" => "param3Str",
		"ks",
		"description",
		"createdAt",
		"updatedAt",
		"userIp",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}