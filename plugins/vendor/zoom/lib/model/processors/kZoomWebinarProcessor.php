<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomWebinarProcessor extends kZoomRecordingProcessor
{
	/**
	 * @param kZoomEvent $event
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingVideoComplete($event)
	{
		if($this->zoomIntegration->getEnableWebinarUploads())
		{
			parent::handleRecordingVideoComplete($event);
		}
		else
		{
			KalturaLog::debug('webinar uploads is disabled for ' . $this->zoomIntegration->getPartnerId());
		}
	}

	protected function getAdditionalUsersFromZoom($accessToken, $recordingId)
	{
		return $this->zoomClient->retrieveWebinarPanelists($accessToken, $recordingId);
	}

	protected function parseAdditionalUsers($additionalUsersZoomResponse)
	{
		$panelists = new kZoomPanelists();
		$panelists->parseData($additionalUsersZoomResponse);
		$panelistsEmails = $panelists->getPanelistsEmails();
		if($panelistsEmails)
		{
			KalturaLog::debug('Found the following panelists: ' . implode(", ", $panelistsEmails));
			$result = array();
			foreach ($panelistsEmails as $panelistEmail)
			{
				$zoomUser = new kZoomUser();
				$zoomUser->setOriginalName($panelistEmail);
				$zoomUser->setProcessedName($this->processZoomUserName($panelistEmail, $this->zoomIntegration));
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
	 * @param entry $entry
	 * @throws kCoreException
	 */
	protected function setEntryCategory($entry)
	{
		if ($this->zoomIntegration->getZoomWebinarCategory())
		{
			$entry->setCategories($this->zoomIntegration->getZoomWebinarCategory());
		}
	}
}