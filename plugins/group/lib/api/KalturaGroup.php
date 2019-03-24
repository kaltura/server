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

	const NAMES = array('fullName','screenName');

	private static $map_between_objects = array("membersCount");

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
		$this->validateNames($this,self::NAMES);
		parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateNames($propertiesToSkip ,self::NAMES);
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function clonedObject($dbOriginalGroup, $newGroupName,  $skip = array())
	{
		$dbObject = new kuser();
		$dbObject->setType(KuserType::GROUP);
		$dbObject->setScreenName($newGroupName);
		$dbObject->setPuserId($newGroupName);
		$dbObject->setTags($dbOriginalGroup->getTags());
		$dbObject->setPartnerId($dbOriginalGroup->getPartnerId());
		$dbObject->setPartnerData($dbOriginalGroup->getPartnerData());
		$dbObject->setStatus($dbOriginalGroup->getStatus());


		parent::toObject($dbObject, $skip);
		return $dbObject;
	}

}