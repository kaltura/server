<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomWebinarProcessor extends zoomRecordingProcessor
{
	
	protected function getAdditionalUsersFromZoom($recordingId)
	{
		return $this->zoomClient->retrieveWebinarPanelists($recordingId);
	}

	protected function getAlternativeHostsData($recordingId)
	{
		return $this->zoomClient->retrieveWebinar($recordingId);
	}

	protected function getCoHostsData($recordingId, $pageSize, $nextPageToken)
	{
		return $this->zoomClient->retrieveMetricsWebinarParticipant($recordingId, $pageSize, $nextPageToken);
	}
	
	protected function parseAdditionalUsers($additionalUsersZoomResponse)
	{
		$panelists = new kZoomPanelists();
		$panelists->parseData($additionalUsersZoomResponse);
		$panelistsEmails = $panelists->getPanelistsEmails();
		return parent::parseZoomEmails($panelistsEmails);
	}
	
	/**
	 * @param KalturaMediaEntry $entry
	 * @param string $meetingId
	 * @throws kCoreException
	 */
	protected function setEntryCategory($entry, $meetingId)
	{
		KBatchBase::impersonate($this->dropFolder->partnerId);
		if ($this->dropFolder->zoomVendorIntegration->zoomWebinarCategory)
		{
			$this->addEntryToCategory($this->dropFolder->zoomVendorIntegration->zoomWebinarCategory, $entry->id);
		}
		KBatchBase::unimpersonate();
	}
}