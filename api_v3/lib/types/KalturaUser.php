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
	 */
	public $password;

	/**
	 * @var string
	 * @filter likex
	 */
	public $firstName;

	/**
	 * @var string
	 * @filter likex
	 */
	public $lastName;

	/**
	 * @var bool
	 * @insertonly
	 */
	public $loginEnabled;

	/**
	 * @var string
	 */
	public $registrationInfo;

	/**
	 * @var string
	 */
	public $attendanceInfo;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
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
		if (strlen($this->fullName) > kuser::MAX_NAME_LEN)
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
