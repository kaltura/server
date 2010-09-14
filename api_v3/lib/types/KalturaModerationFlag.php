<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaModerationFlag extends KalturaObject 
{
	/**
	 * Moderation flag id
	 *
	 * @var int
	 * @readonly
	 */
	public $id;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The user id that added the moderation flag
	 *
	 * @var string
	 * @readonly
	 */
	public $userId;

	/**
	 * The type of the moderation flag (entry or user)
	 *
	 * @var KalturaModerationObjectType
	 * @readonly
	 */
	public $moderationObjectType; // can't be objectType because it is reserved for the type of the object in the api
	
	/**
	 * If moderation flag is set for entry, this is the flagged entry id
	 *
	 * @var string
	 */	
	public $flaggedEntryId;
	
	/**
	 * If moderation flag is set for user, this is the flagged user id
	 *
	 * @var string
	 */	
	public $flaggedUserId;
	
	/**
	 * The moderation flag status
	 *
	 * @var KalturaModerationFlagStatus
	 * @readonly
	 */
	public $status;
	
	/**
	 * The comment that was added to the flag
	 *
	 * @var string
	 */
	public $comments;
	
	/**
	 * @var KalturaModerationFlagType
	 */
	public $flagType;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $createdAt;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $updatedAt;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"userId" => "puserId",
		"moderationObjectType" => "objectType",
		"flaggedEntryId",
		"flaggedUserId" => "flaggedPuserId",
		"status",
		"comments",
		"flagType",
		"createdAt",
		"updatedAt"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
?>