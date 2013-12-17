<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaDeleteEntryFlavorsObjectTask extends KalturaObjectTask
{
	/**
	 * The logic to use to choose the flavors for deletion
	 *
	 * @var KalturaDeleteFlavorsLogicType
	 */
	public $deleteType;

	/**
	 * Comma separated list of flavor param ids to delete or keep
	 *
	 * @var string
	 */
	public $flavorParams;

	public function __construct()
	{
		$this->type = ObjectTaskType::DELETE_ENTRY_FLAVORS;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('deleteType', $this->deleteType);
		$dbObject->setDataValue('flavorParams', $this->flavorParams);
		return $dbObject;
	}

	public function fromObject($srcObj)
	{
		parent::fromObject($srcObj);

		/** @var kObjectTask $srcObj */
		$this->deleteType = $srcObj->getDataValue('deleteType');
		$this->flavorParams = $srcObj->getDataValue('flavorParams');
	}
}