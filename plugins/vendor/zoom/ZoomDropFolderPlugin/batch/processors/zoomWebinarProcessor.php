<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomWebinarProcessor extends zoomRecordingProcessor
{
	/**
	 * @param KalturaZoomDropFolderFile $recording
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingVideoComplete($recording)
	{
		if($this->dropFolder->zoomVendorIntegration->enableWebinarUploads)
		{
			parent::handleRecordingVideoComplete($recording);
		}
		else
		{
			KalturaLog::debug('webinar uploads is disabled for ' . $this->dropFolder->partnerId);
		}
	}

	protected function getAdditionalUsersFromZoom($recordingId)
	{
		return $this->zoomClient->retrieveWebinarPanelists($recordingId);
	}

	protected function parseAdditionalUsers($additionalUsersZoomResponse)
	{
		$panelists = new kZoomPanelists();
		$panelists->parseData($additionalUsersZoomResponse);
		$panelistsEmails = $panelists->getPanelistsEmails();
		if($panelistsEmails)
		{
			KalturaLog::debug('Found the following panelists: ' . implode(', ', $panelistsEmails));
			$result = array();
			foreach ($panelistsEmails as $panelistEmail)
			{
				$zoomUser = new kZoomUser();
				$zoomUser->setOriginalName($panelistEmail);
				$zoomUser->setProcessedName($this->processZoomUserName($panelistEmail));
				$result[] = $zoomUser;
			}
		}
		else
		{
			$result = null;
		}

		return $result;
	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @throws kCoreException
	 */
	protected function setEntryCategory($entry)
	{
		if ($this->dropFolder->zoomVendorIntegration->zoomWebinarCategory)
		{
			$entry->categories = $this->dropFolder->zoomVendorIntegration->zoomWebinarCategory;
		}
	}
}