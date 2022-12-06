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
	
	protected function parseAdditionalUsers($additionalUsersZoomResponse)
	{
		$panelists = new kZoomPanelists();
		$panelists->parseData($additionalUsersZoomResponse);
		$panelistsEmails = $panelists->getPanelistsEmails();
		$result = array();
		if($panelistsEmails)
		{
			KalturaLog::debug('Found the following panelists: ' . implode(", ", $panelistsEmails));
			foreach ($panelistsEmails as $panelistEmail)
			{
				$zoomUser = new kZoomUser();
				$zoomUser->setOriginalName($panelistEmail);
				$zoomUser->setProcessedName(ZoomBatchUtils::processZoomUserName($panelistEmail, $this->dropFolder->zoomVendorIntegration, $this->zoomClient));
				$result[] = $zoomUser;
			}
		}
		
		return $result;
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