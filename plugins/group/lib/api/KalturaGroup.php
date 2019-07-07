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

	private static $names = array('fullName','screenName');

	private static $map_between_objects = array('membersCount', 'processStatus');

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new kuser();
			$dbObject->setType(KuserType::GROUP);
		}
		parent::toObject($dbObject, $skip);
		return $dbObject;
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$id = $this->id;
		if (!$this->id && $propertiesToSkip->getPuserId())
		{
			$id = $propertiesToSkip->getPuserId();
		}
		if (!preg_match(kuser::PUSER_ID_REGEXP, $id))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'id');
		}

		$this->validateNames($this,self::$names);
		parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateNames($sourceObject ,self::$names);
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function clonedObject($dbOriginalGroup, $newGroupId, $newGroupName)
	{
		$dbObject = $this->toObject();

		$dbObject->setScreenName($newGroupName);
		$dbObject->setPuserId($newGroupId);
		$dbObject->setTags($dbOriginalGroup->getTags());
		$dbObject->setPartnerId($dbOriginalGroup->getPartnerId());
		$dbObject->setPartnerData($dbOriginalGroup->getPartnerData());
		$dbObject->setStatus($dbOriginalGroup->getStatus());
		$dbObject->setEmail($dbOriginalGroup->getEmail());
		$dbObject->setLanguage($dbOriginalGroup->getLanguage());
		$dbObject->setPicture($dbOriginalGroup->getPicture());
		$dbObject->setAboutMe($dbOriginalGroup->getAboutMe());


		return $dbObject;
	}

}