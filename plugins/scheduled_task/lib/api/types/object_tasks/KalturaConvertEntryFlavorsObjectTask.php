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
	public $flavorParamsIds;

	/**
	 * Should reconvert when flavor already exists?
	 *
	 * @var bool
	 */
	public $reconvert;

	public function __construct()
	{
		$this->type = ObjectTaskType::CONVERT_ENTRY_FLAVORS;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);

		$flavorParamsIds = array_unique(kString::fromCommaSeparatedToArray($this->flavorParamsIds));
		$dbObject->setDataValue('flavorParamsIds', $flavorParamsIds);
		$dbObject->setDataValue('reconvert', $this->reconvert);
		return $dbObject;
	}

	public function fromObject($srcObj)
	{
		parent::fromObject($srcObj);

		/** @var kObjectTask $srcObj */
		$this->flavorParamsIds = implode(',', $srcObj->getDataValue('flavorParamsIds'));
		$this->reconvert = $srcObj->getDataValue('reconvert');
	}
}