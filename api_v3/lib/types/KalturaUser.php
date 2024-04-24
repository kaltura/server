<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService UserService
 */
class KalturaUser extends KalturaBaseUser
{
	private static $names = array('firstName' => 'getFirstName', 'lastName' => 'getLastName', 'fullName' => 'getFullName', 'screenName' => 'getScreenName');

	/**
	 * @var KalturaUserType
	 * @filter eq,in
	 */
	public $type;

	/**
	 * @var int
	 * @masked
	 */
	public $dateOfBirth;

	/**
	 * @var KalturaGender
	 */
	public $gender;

	/**
	 * @var bool
	 * @filter eq
	 */
	public $isAdmin;

	/**
	 * @var bool
	 * @insertonly
	 */
	public $isGuest;

	/**
	 * @var string
	 */
	public $roleIds;

	/**
	 * @var string
	 * @readonly
	 */
	public $roleNames;


	/**
	 * @var bool
	 * @insertonly
	 */
	public $isAccountOwner;

	/**
	 * @var string
	 * @insertonly
	 * @writeonly
	 * @masked
	 */
	public $password;

	/**
	 * @var string
	 * @filter likex
	 * @masked
	 */
	public $firstName;

	/**
	 * @var string
	 * @filter likex
	 * @masked
	 */
	public $lastName;

	/**
	 * @var bool
	 * @insertonly
	 */
	public $loginEnabled;

	/**
	 * @var string
	 * @masked
	 * @maskingMaxLength 256
	 */
	public $registrationInfo;

	/**
	 * @var string
	 * @maskingMaxLength 256
	 */
	public $attendanceInfo;

	/**
	 * @var string
	 * @masked
	 */
	public $title;

	/**
	 * @var string
	 * @masked
	 */
	public $company;

	/**
	 * @var string
	 */
	public $ksPrivileges;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $encryptedSeed;
	
	/**
	 * @var bool
	 */
	public $isSsoExcluded;
	
	/**
	 * This field should be sent instead of the id field whenever you want to work with hashed user ids
	 * @var string
	 * @insertonly
	 * @masked
	 */
	public $externalId;
	
	private static $map_between_objects = array (
		"type",
		"dateOfBirth",
		"gender",
		"firstName",
		"lastName",
		"isAdmin",
		"roleIds",
		"roleNames" => "userRoleNames",
		"isAccountOwner",
		"registrationInfo",
		"attendanceInfo",
		"title",
		"company",
		"ksPrivileges",
		"isSsoExcluded",
		"isGuest",
		"externalId",
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
		if ($this->fullName && !$this->firstName) {
			list($firstName, $lastName) = kString::nameSplit($this->fullName);
			$dbObject->setFirstName($firstName);
			$dbObject->setLastName($lastName);
		}
		
		if($this->externalId)
		{
			$hashedUserId = myKuserUtils::getHashedUserId($this->externalId);
			$dbObject->setPuserId($hashedUserId);
			if($hashedUserId != $this->externalId)
			{
				$dbObject->setExternalId($this->externalId);
				$dbObject->setIsHashed(true);
			}
		}

		return $dbObject;
	}
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);

		// full name is deprecated and was split to firstName + lastName
		// this is for backward compatibility
		if($this->shouldGet('fullName', $responseProfile))
			$this->fullName = $sourceObject->getFullName();
		if($this->shouldGet('loginEnabled', $responseProfile))
			$this->loginEnabled = !is_null($sourceObject->getLoginDataId());
	}
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->verifyMaxLength();
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function toUpdatableObject($object_to_fill, $props_to_skip = array())
	{
		$this->verifyMaxLength();
		return parent::toUpdatableObject($object_to_fill, $props_to_skip);
	}

	private function verifyMaxLength()
	{
		if ($this->fullName && strlen($this->fullName) > kuser::MAX_NAME_LEN)
			$this->fullName = kString::alignUtf8String($this->fullName, kuser::MAX_NAME_LEN);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateNames(null, self::$names);
		parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateNames($sourceObject, self::$names);
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
