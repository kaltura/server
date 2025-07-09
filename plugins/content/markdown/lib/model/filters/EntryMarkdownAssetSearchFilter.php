<?php
/**
 * @package plugins.markdown
 * @subpackage model.filters
 */
class EntryMarkdownAssetSearchFilter extends EntryAssetSearchFilter
{

	public function createSphinxMatchPhrase($text)
	{
		$condition = MarkdownPlugin::ENTRY_MARKDOWN_PREFIX . "<<$text<<" . MarkdownPlugin::ENTRY_MARKDOWN_SUFFIX;
		$prefix = '@' . MarkdownPlugin::PLUGINS_DATA;
		return $prefix . " " . $condition;
	}

}
