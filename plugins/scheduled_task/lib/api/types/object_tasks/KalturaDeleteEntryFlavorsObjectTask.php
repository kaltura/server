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
	public $flavorParamsIds;

	public function __construct()
	{
		$this->type = ObjectTaskType::DELETE_ENTRY_FLAVORS;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);

		$flavorParamsIds = array_unique(kString::fromCommaSeparatedToArray($this->flavorParamsIds));
		$dbObject->setDataValue('deleteType', $this->deleteType);
		$dbObject->setDataValue('flavorParamsIds', $flavorParamsIds);
		return $dbObject;
	}

	public function fromObject($srcObj, KalturaResponseProfileBase $responseProfile = null)
	{
		parent::fromObject($srcObj, $responseProfile);

		/** @var kObjectTask $srcObj */
		$this->deleteType = $srcObj->getDataValue('deleteType');
		$this->flavorParamsIds = implode(',', $srcObj->getDataValue('flavorParamsIds'));
	}
}