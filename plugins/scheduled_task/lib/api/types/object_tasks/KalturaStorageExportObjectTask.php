<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaStorageExportObjectTask extends KalturaObjectTask
{
	/**
	 * Storage profile id
	 *
	 * @var string
	 */
	public $storageId;

	public function __construct()
	{
		$this->type = ObjectTaskType::STORAGE_EXPORT;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);

		$dbObject->setDataValue('storageId', $this->storageId);
		return $dbObject;
	}

	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);

		/** @var kObjectTask $srcObj */
		$this->storageId = $srcObj->getDataValue('storageId');
	}
}