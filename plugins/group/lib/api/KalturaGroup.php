<?php
/**
 * @package plugins.group
 * @subpackage api.objects
 * @relatedService GroupService
 */
class KalturaGroup extends KalturaBaseUser
{
	/**
	 * @var int
	 * @readonly
	 */
	public $membersCount;

	/**
	 * @var KalturaGroupProcessStatus
	 */
	public $processStatus;

	/**
	 * @var KalturaGroupType
	 */
	public $groupType;

	private static $names = array('fullName' => 'getFullName', 'screenName' => 'getScreenName');

	private static $map_between_objects = array
	(
		'membersCount',
		'processStatus',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new kuser();
			$groupType = $this->groupType ?? KuserType::GROUP;
			$dbObject->setType($groupType);
		}
		parent::toObject($dbObject, $skip);
		return $dbObject;
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if (!preg_match(kuser::PUSER_ID_REGEXP, $this->id))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'id');
		}

		$this->validateNames(null, self::$names);
		parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateNames($sourceObject, self::$names);
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function clonedObject($dbOriginalGroup, $newGroupId, $newGroupName)
	{
		$this->screenName = $newGroupName;
		$this->id = $newGroupId;
		$this->tags = $dbOriginalGroup->getTags();
		$this->partnerData = $dbOriginalGroup->getPartnerData();
		$this->status = $dbOriginalGroup->getStatus();
		$this->email = $dbOriginalGroup->getEmail();
		$this->language = $dbOriginalGroup->getLanguage();
		$this->thumbnailUrl = $dbOriginalGroup->getPicture();
		$this->description = $dbOriginalGroup->getAboutMe();

		$dbObject = $this->toInsertableObject();
		$dbObject->setPartnerId($dbOriginalGroup->getPartnerId());
		return $dbObject;
	}
}
