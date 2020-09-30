<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kResizePlaylistAdapter extends kBaseResizeAdapter
{
	protected function preTransformationExtraActions()
	{
		parent::preTransformationExtraActions();
		$entry = $this->getEntry();
		$sourceEntry = myPlaylistUtils::getFirstEntryFromPlaylist($entry);
		if ($sourceEntry)
		{
			$this->parameters->set(kThumbFactoryFieldName::SOURCE_ENTRY, $sourceEntry);
		}
		myPlaylistUtils::updatePlaylistStatistics($entry->getPartnerId(), $entry);
	}
}