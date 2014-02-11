<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
 */ 
class ScheduledTaskBatchType implements IKalturaPluginEnum, BatchJobType
{
	const SCHEDULED_TASK = 'ScheduledTask';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SCHEDULED_TASK' => self::SCHEDULED_TASK,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array();
	}
}
