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
		$zoomIntegration = ZoomHelper::getZoomIntegration();
		if($zoomIntegration->getEnableWebinarUploads())
		{
			parent::handleRecordingVideoComplete($event);
		}
		else
		{
			KalturaLog::debug('webinar uploads is disabled for ' . $zoomIntegration->getPartnerId());
		}
	}

	protected function getAdditionalUsersFromZoom($accessToken, $recordingId)
	{
		return $this->zoomClient->retrieveWebinarPanelists($accessToken, $recordingId);
	}

	protected function parseAdditionalZoomUsers($additionalUsersZoomResponse, $userToExclude, $zoomIntegration)
	{
		$panelists = new kZoomPanelists();
		$panelists->parseData($additionalUsersZoomResponse);
		$panelistsEmails = $panelists->getPanelistsEmails();
		if($panelistsEmails)
		{
			KalturaLog::debug('Found the following panelists: ' . implode(", ", $panelistsEmails));
			$result = array();
			foreach ($panelistsEmails as $panelistEmails)
			{
				$userName = $this->matchZoomUserName($panelistEmails, $zoomIntegration);
				if($userToExclude !== strtolower($userName))
				{
					$result[] = $userName;
				}
			}
		}
		else
		{
			$result = null;
		}

		return $result;
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param entry $entry
	 * @throws kCoreException
	 */
	protected function setEntryCategory($zoomIntegration, $entry)
	{
		if ($zoomIntegration->getZoomWebinarCategory())
		{
			$entry->setCategories($zoomIntegration->getZoomWebinarCategory());
		}
	}
}