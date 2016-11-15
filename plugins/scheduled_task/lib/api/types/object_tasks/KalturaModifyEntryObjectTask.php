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
	 * comma-separated values of {input metadata xpath location,entry field}
	 *
	 * @var KalturaStringArray
	 */
	public $inputMetadata;

	/**
	 * The output metadata profile id
	 *
	 * @var int
	 */
	public $outputMetadataProfileId;

	/**
	 * comma-separated values of {output metadata xpath location,entry field}
	 *
	 * @var KalturaStringArray
	 */
	public $outputMetadata;

	/**
	 * comma-separated values of {value,entry field}
	 *
	 * @var KalturaStringArray
	 */
	public $fieldValues;

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
		$dbObject->setDataValue('fieldValues', $this->fieldValues);
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
		$this->fieldValues = $srcObj->getDataValue('fieldValues');
	}
}