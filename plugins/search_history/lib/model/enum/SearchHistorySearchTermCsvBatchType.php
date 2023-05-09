<?php
/**
 * @package plugins.searchHistory
 * @subpackage model.enum
 */
class SearchHistorySearchTermCsvBatchType implements IKalturaPluginEnum, BatchJobType
{
	const SEARCH_TERMS_CSV = 'searchTermsCsv';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SEARCH_TERM_CSV' => self::SEARCH_TERMS_CSV,
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