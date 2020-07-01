<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kResizeStitchedPlaylistAdapter extends kBaseResizeAdapter
{
	protected function getEntryLengthInMS()
	{
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		list($entryIds, $durations, $mediaEntry, $captionFiles) = myPlaylistUtils::executeStitchedPlaylist($entry);
		return array_sum($durations);
	}
}