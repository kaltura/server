<?php
/**
 * @package plugins.transcript
 * @subpackage model.filters
 */
class EntryTranscriptAssetSearchFilter extends EntryAssetSearchFilter
{

	public function createSphinxMatchPhrase($text)
	{
		$condition = TranscriptPlugin::ENTRY_TRANSCRIPT_PREFIX . "<<$text<<" . TranscriptPlugin::ENTRY_TRANSCRIPT_SUFFIX ;
		$prefix = '@' . TranscriptPlugin::PLUGINS_DATA;
		return $prefix . " " . $condition;
	}

}
