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
		myPlaylistUtils::updatePlaylistStatistics($entry->getPartnerId(), $entry);
	}
}