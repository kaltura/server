<?php
/**
* @package api
* @subpackage objects
* @relatedService UserService
*/
class KalturaBaseUser extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var string
	 * @filter order
	 * @masked
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
	 * @utf8truncate 127
	 * @masked
	 */
	public $screenName;

	/**
	 * @var string
	 * @utf8truncate 40
	 * @deprecated
	 * @masked
	 */
	public $fullName;

	/**
	 * @var string
	 * @filter like,likex
	 * @masked
	 */
	public $email;

	/**
	 * @var string
	 * @masked
	 */
	public $country;

	/**
	 * @var string
	 * @utf8truncate 16
	 * @masked
	 */
	public $state;

	/**
	 * @var string
	 * @utf8truncate 30
	 * @masked
	 */
	public $city;

	/**
	 * @var string
	 * @utf8truncate 10
	 * @masked
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
	 * @deprecated Use "tags" field instead.
	 * @var string
	 */
	public $adminTags;

	/**
	 * @var KalturaUserStatus
	 * @filter eq,in
	 */
	public $status;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
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
	 * @var KalturaLanguageCode
	 */
	public $language;

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
	 * @var time
	 * @readonly
	 */
	public $deletedAt;


	/**
	 * @var string
	 */
	public $allowedPartnerIds;

	/**
	 * @var string
	 */
	public $allowedPartnerPackages;

	/**
	 * @var KalturaUserMode
	 */
	public $userMode;
	
	/**
	 * @var KalturaUserCapabilityArray
	 */
	public $capabilities;

	private static $map_between_objects = array
	(
		"id" => "puserId",
		"partnerId",
		"screenName",
		"email",
		"country",
		"state",
		"city",
		"zip",
		"thumbnailUrl" => "picture",
		"description" => "aboutMe",
		"tags",
		"status",
		"createdAt",
		"updatedAt",
		"partnerData",
		"storageSize",
		"language",
		"lastLoginTime",
		"deletedAt",
		"allowedPartnerIds" => "allowedPartners",
		"allowedPartnerPackages",
		"statusUpdatedAt",
		"userMode",
		"capabilities",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}


	public function getExtraFilters()
	{
		return array();
	}

	public function getFilterDocs()
	{
		return array();
	}

	/**
	 * @param $coreObject The object to validate
	 * @param $names array of names
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 */
	protected function validateNames($coreObject, array $names)
	{
		foreach ($names as $kalturaProperty => $dbGetFunction)
		{
			if(is_null($this->$kalturaProperty))
			{
				continue;
			}
			
			if(strpos($this->$kalturaProperty, kuser::URL_PATTERN) !== false)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, $kalturaProperty);
			}
			
			if($coreObject)
			{
				$dbVal = $coreObject->$dbGetFunction();
				if( !is_null($dbVal) && ($dbVal === $this->$kalturaProperty) )
				{
					continue;
				}
			}
		}
	}
}
