<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
 */
class ScheduledTaskBatchJobObjectType implements IKalturaPluginEnum, BatchJobObjectType
{
	const SCHEDULED_TASK_PROFILE		= "ScheduledTaskProfile";

	public static function getAdditionalValues()
	{
		return array(
			'SCHEDULED_TASK_PROFILE' => self::SCHEDULED_TASK_PROFILE,
		);
	}

	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
