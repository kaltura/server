<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaModifyEntryObjectTask extends KalturaObjectTask
{
	/**
	 * The input metadata profile id
	 *
	 * @var int
	 */
	public $inputMetadataProfileId;

	/**
	 * array of {input metadata xpath location,entry field} objects
	 *
	 * @var KalturaKeyValueArray
	 */
	public $inputMetadata;

	/**
	 * The output metadata profile id
	 *
	 * @var int
	 */
	public $outputMetadataProfileId;

	/**
	 * array of {output metadata xpath location,entry field} objects
	 *
	 * @var KalturaKeyValueArray
	 */
	public $outputMetadata;

	/**
	 * The input user id to set on the entry
	 *
	 * @var string
	 */
	public $inputUserId;
	
	/**
	 * The input entitled users edit to set on the entry
	 *
	 * @var string
	 */
	public $inputEntitledUsersEdit;
	
	/**
	 * The input entitled users publish to set on the entry
	 *
	 * @var string
	 */
	public $inputEntitledUsersPublish;

	/**
	 * Should clear the media repurposing data and therefore reset the process
	 *
	 * @var bool
	 */
	public $resetMediaRepurposingProcess;

	public function __construct()
	{
		$this->type = ObjectTaskType::MODIFY_ENTRY;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('inputMetadataProfileId', $this->inputMetadataProfileId);
		$dbObject->setDataValue('inputMetadata', $this->inputMetadata);
		$dbObject->setDataValue('outputMetadataProfileId', $this->outputMetadataProfileId);
		$dbObject->setDataValue('outputMetadata', $this->outputMetadata);
		$dbObject->setDataValue('inputUserId', $this->inputUserId);
		$dbObject->setDataValue('inputEntitledUsersEdit', $this->inputEntitledUsersEdit);
		$dbObject->setDataValue('inputEntitledUsersPublish', $this->inputEntitledUsersPublish);
		$dbObject->setDataValue('resetMediaRepurposingProcess', $this->resetMediaRepurposingProcess);
		return $dbObject;
	}

	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);

		/** @var kObjectTask $srcObj */
		$this->inputMetadataProfileId = $srcObj->getDataValue('inputMetadataProfileId');
		$this->inputMetadata = $srcObj->getDataValue('inputMetadata');
		$this->outputMetadataProfileId = $srcObj->getDataValue('outputMetadataProfileId');
		$this->outputMetadata = $srcObj->getDataValue('outputMetadata');
		$this->inputUserId = $srcObj->getDataValue('inputUserId');
		$this->inputEntitledUsersEdit = $srcObj->getDataValue('inputEntitledUsersEdit');
		$this->inputEntitledUsersPublish = $srcObj->getDataValue('inputEntitledUsersPublish');
		$this->resetMediaRepurposingProcess = $srcObj->getDataValue('resetMediaRepurposingProcess');
	}
}