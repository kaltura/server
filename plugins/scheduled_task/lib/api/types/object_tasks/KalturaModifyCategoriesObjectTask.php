<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaModifyCategoriesObjectTask extends KalturaObjectTask
{
	/**
	 * Should the object task add or remove categories?
	 *
	 * @var KalturaScheduledTaskAddOrRemoveType
	 */
	public $addRemoveType;

	/**
	 * The list of category ids to add or remove
	 *
	 * @var KalturaIntegerValueArray
	 */
	public $categoryIds;

	public function __construct()
	{
		$this->type = ObjectTaskType::MODIFY_CATEGORIES;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('addRemoveType', $this->addRemoveType);
		$dbObject->setDataValue('categoryIds', $this->categoryIds);
		return $dbObject;
	}

	public function fromObject($srcObj, KalturaResponseProfileBase $responseProfile = null)
	{
		parent::fromObject($srcObj, $responseProfile);

		/** @var kObjectTask $srcObj */
		$this->addRemoveType = $srcObj->getDataValue('addRemoveType');
		$this->categoryIds = $srcObj->getDataValue('categoryIds');
	}
}