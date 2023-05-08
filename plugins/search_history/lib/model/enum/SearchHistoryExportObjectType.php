<?php
/**
 * @package plugins.searchHistory
 * @subpackage model.enum
 */
class SearchHistoryExportObjectType implements IKalturaPluginEnum, ExportObjectType
{
	const SEARCH_TERM = 'searchTerm';

	public static function getAdditionalValues()
	{
		return array(
			'SEARCH_TERM' => self::SEARCH_TERM,
		);
	}

	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			SearchHistoryPlugin::getApiValue(self::SEARCH_TERM) => 'Search Term',
		);
	}

}