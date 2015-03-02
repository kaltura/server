<?php

/**
 * @package plugins.scheduledTaskContentDistribution
 * @subpackage api.objects.objectTasks
 */
class KalturaDistributeObjectTask extends KalturaObjectTask
{
	/**
	 * Distribution profile id
	 *
	 * @var string
	 */
	public $distributionProfileId;

	public function __construct()
	{
		$this->type = ScheduledTaskContentDistributionPlugin::getApiValue(DistributeObjectTaskType::DISTRIBUTE);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);

		$dbObject->setDataValue('distributionProfileId', $this->distributionProfileId);
		return $dbObject;
	}

	public function fromObject($srcObj)
	{
		parent::fromObject($srcObj);

		/** @var kObjectTask $srcObj */
		$this->distributionProfileId = $srcObj->getDataValue('distributionProfileId');
	}
}