<?php
/**
 * @package plugins.viewHistory
 * @subpackage model.enum
 */
class ViewHistoryUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const VIEW_HISTORY = "VIEW_HISTORY";
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			"VIEW_HISTORY" => self::VIEW_HISTORY,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::VIEW_HISTORY => 'View History User Entry Type',
		);
	}
}