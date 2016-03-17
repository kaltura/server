<?php
/**
 * @package plugins.caption
 * @subpackage model.filters
 */
class EntryCaptionAssetSearchFilter extends EntryAssetSearchFilter
{
	public function createSphinxMatchPhrase($text)
	{
		$condition = "ca_prefix<<$text<<ca_sufix";
		$prefix = '@' . CaptionSearchPlugin::getSearchFieldName(CaptionSearchPlugin::SEARCH_FIELD_DATA);
		return $prefix . ' ' . $condition;
	}

}
