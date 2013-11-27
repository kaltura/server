<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaConvertEntryFlavorsObjectTask extends KalturaObjectTask
{
	/**
	 * Comma separated list of flavor param ids to convert
	 *
	 * @var string
	 */
	public $flavorParams;

	/**
	 * Should reconvert a flavor if it already exists
	 *
	 * @var bool
	 */
	public $shouldReconvert;

	public function __construct()
	{
		$this->type = ObjectTaskType::CONVERT_ENTRY_FLAVORS;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('flavorParams', $this->flavorParams);
		$dbObject->setDataValue('shouldReconvert', $this->shouldReconvert);
		return $dbObject;
	}

	public function fromObject($srcObj)
	{
		parent::fromObject($srcObj);

		/** @var kObjectTask $srcObj */
		$this->flavorParams = $srcObj->getDataValue('flavorParams');
		$this->shouldReconvert = $srcObj->getDataValue('shouldReconvert');
	}
}