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
				$zoomUser->setProcessedName($this->processZoomUserName($panelistEmail));
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
		$categories = array();
		$categoryTrackingField = $this->zoomClient->retrieveTrackingField($meetingId);
		if ($categoryTrackingField)
		{
			$categories[] = $categoryTrackingField;
		}
		if ($this->dropFolder->zoomVendorIntegration->zoomWebinarCategory)
		{
			$categories[] = $this->dropFolder->zoomVendorIntegration->zoomWebinarCategory;
		}
		if ($categories)
		{
			$entry->categories = implode(',', $categories);
		}
	}
}