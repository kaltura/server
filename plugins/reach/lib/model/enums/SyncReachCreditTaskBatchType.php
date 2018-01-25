<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
class SyncReachCreditTaskBatchType implements IKalturaPluginEnum, BatchJobType
{
	const SYNC_REACH_CREDIT_TASK = 'SyncReachCreditTask';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SYNC_REACH_CREDIT_TASK' => self::SYNC_REACH_CREDIT_TASK,
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
