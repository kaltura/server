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
	 * The list of categories to add or remove
	 *
	 * @var KalturaStringArray
	 */
	public $categories;

	public function __construct()
	{
		$this->type = ObjectTaskType::MODIFY_CATEGORIES;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('addRemoveType', $this->addRemoveType);
		$dbObject->setDataValue('categories', $this->categories);
		return $dbObject;
	}

	public function fromObject($srcObj)
	{
		parent::fromObject($srcObj);

		/** @var kObjectTask $srcObj */
		$this->addRemoveType = $srcObj->getDataValue('addRemoveType');
		$this->categories = $srcObj->getDataValue('categories');
	}
}