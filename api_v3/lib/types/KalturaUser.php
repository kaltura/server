<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUser extends KalturaObject implements IFilterable 
{
	/**
	 * @var string
	 * @filter order
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
	 * @deprecated
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
	 * @filter eq,in
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
	
	/**
	 * @var string
	 * @insertonly
	 * @writeonly
	 */
	public $password;
	
	/**
	 * @var string
	 */
	public $firstName;
	
	/**
	 * @var string
	 */
	public $lastName;
		
	/**
	 * @var bool
	 * @filter eq
	 */
	public $isAdmin;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $lastLoginTime;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $statusUpdatedAt;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $deletedAt;
	
	/**
	 * @var bool
	 * @readonly
	 */
	public $loginEnabled;
	
	
	/**
	 * @var string
	 */
	public $roleIds;

	

	private static $map_between_objects = array
	(
		"id" => "puserId", 
		"partnerId",
		"screenName",
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
		"storageSize",
		"firstName",
		"lastName",
		"isAdmin",
		"lastLoginTime",
		"statusUpdatedAt",
		"deletedAt",
		"roleIds",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kuser();
			
		parent::toObject($dbObject, $skip);
		
		// full name is deprecated and was split to firstName + lastName
		// this is for backward compatibility with older clients
		if ($this->fullName || !$this->firstName) {
			list($firstName, $lastName) = kString::nameSplit($this->fullName);
			$dbObject->setFirstName($firstName);
			$dbObject->setLastName($lastName);
		}
		
		return $dbObject;		
	}
	
	
	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
		// full name is deprecated and was split to firstName + lastName
		// this is for backward compatibility
		$this->fullName = $sourceObject->getFullName();
		$this->loginEnabled = !is_null($sourceObject->getLoginDataId());
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