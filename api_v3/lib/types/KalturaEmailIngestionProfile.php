<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEmailIngestionProfile extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $emailAddress;
	
	/**
	 * @var string
	 */
	public $mailboxId;

	/**
	 * @var int
	 */
	public $partnerId;

	/**
	 * @var int
	 */
	public $conversionProfile2Id;
	
	/**
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatus;
	
	/**
	 * @var KalturaEmailIngestionProfileStatus
	 * @readonly
	 */
	public $status;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var string
	 */
	public $defaultCategory;
	
	/**
	 * @var string
	 */
	public $defaultUserId;
	
	/**
	 * @var string
	 */
	public $defaultTags;
	
	/**
	 * @var string
	 */
	public $defaultAdminTags;
	
	/**
	 * @var int
	 */
	public $maxAttachmentSizeKbytes;
	
	/**
	 * @var int
	 */
	public $maxAttachmentsPerMail;
	
	
	private static $map_between_objects = array
	(
		"id" , "name", "description", "emailAddress" , "mailboxId" , "partnerId" ,
		"conversionProfile2Id" , "moderationStatus" , "createdAt" , "status" , 
		"defaultCategory" , "defaultUserId" , "defaultTags" , "defaultAdminTags" ,
		"maxAttachmentSizeKbytes" , "maxAttachmentsPerMail"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	public function fromEmailIngestionProfile(EmailIngestionProfile $eip)
	{
		parent::fromObject($eip);
		return $this;
	}
	
	public function toEmailIngestionProfile()
	{
		$eip = new EmailIngestionProfile();
		return parent::toObject( $eip );
	}
}



