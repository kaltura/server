<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */
class EntryVendorTaskExportObjectType implements IKalturaPluginEnum, ExportObjectType
{
	const ENTRY_VENDOR_TASK = 'entryVendorTask';
	
	public static function getAdditionalValues()
	{
		return array(
			'ENTRY_VENDOR_TASK' => self::ENTRY_VENDOR_TASK,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			ReachPlugin::getApiValue(self::ENTRY_VENDOR_TASK) => 'Entry Vendor Task',
		);
	}
}