<?php

/**
 * @package plugins.reach
 * @subpackage model.enum
 */
class ReachEntryVendorTasksCsvBatchType implements IKalturaPluginEnum, BatchJobType
{
	const ENTRY_VENDOR_TASK_CSV = 'EntryVendorTasksCsv';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ENTRY_VENDOR_TASK_CSV' => self::ENTRY_VENDOR_TASK_CSV,
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
