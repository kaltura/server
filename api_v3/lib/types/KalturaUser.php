<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService UserService
 */
class KalturaUser extends KalturaBaseUser
{
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

	private static $map_between_objects = array (
		"type",
		"dateOfBirth",
		"gender",
		"firstName",
		"lastName",
		"isAdmin",
		"roleIds",
		"roleNames" => "userRoleNames",
		"isAccountOwner"
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


}
