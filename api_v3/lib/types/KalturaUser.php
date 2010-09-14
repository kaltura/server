<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUser extends KalturaObject implements IFilterable 
{
	/**
	 * @var string
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var int
	 * @readonly
	 * @filter eq
	 */
	public $partnerId;
	
	/**
	 * @var string
	 * @filter like,likex
	 */
	public $screenName;

	/**
	 * @var string
	 */
	public $fullName;

	/**
	 * @var string
	 * @filter like,likex
	 */
	public $email;

	/**
	 * @var int
	 */
	public $dateOfBirth;
	
	/**
	 * @var string
	 */
	public $country;

	/**
	 * @var string
	 */
	public $state;

	/**
	 * @var string
	 */
	public $city;

	/**
	 * @var string
	 */
	public $zip;
	
	/**
	 * @var string
	 */
	public $thumbnailUrl;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 * @filter mlikeor,mlikeand
	 */
	public $tags;
	
	/**
	 * Admin tags can be updated only by using an admin session
	 *
	 * @var string
	 */
	public $adminTags;
	
	/**
	 * @var KalturaGender
	 */
	public $gender;

	/**
	 * @var KalturaUserStatus
	 */
	public $status;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 * @var int
	 * @readonly
	 */
	public $updatedAt;

	/**
	 * Can be used to store various partner related data as a string 
	 * @var string
	 */
	public $partnerData;
	
	/**
	 * @var int
	 */
	public $indexedPartnerDataInt;
	
	/**
	 * @var string
	 */
	public $indexedPartnerDataString;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $storageSize;

	private static $map_between_objects = array
	(
		"id" => "puserId", 
		"partnerId",
		"screenName",
		"fullName",
		"email",
		"dateOfBirth",
		"country",
		"state",
		"city",
		"zip",
		"thumbnailUrl" => "picture",
		"description" => "aboutMe",
		"tags",
		"gender",
		"status",
		"createdAt",
		"updatedAt",
		"partnerData",
		"storageSize"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function fromUser ( kuser $entry )
	{
		parent::fromObject( $entry );
		$this->dateOfBirth = $entry->getDateOfBirth();
	}
	
	public function toUser () 
	{
		$user = new kuser();
		return parent::toObject( $user );
	}
	
	public function getExtraFilters()
	{ 
		return array();		
	}
	
	public function getFilterDocs()
	{
		return array();	
	}
}
?>