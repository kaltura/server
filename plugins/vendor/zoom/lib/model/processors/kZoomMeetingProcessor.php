<?php
/**
* @package plugins.vendor
* @subpackage zoom.model
*/

class kZoomMeetingProcessor extends kZoomRecordingProcessor
{
	protected function getAdditionalUsersFromZoom($accessToken, $recordingId)
	{
		return $this->zoomClient->retrieveMeetingParticipant($accessToken, $recordingId);
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param entry $entry
	 * @throws kCoreException
	 */
	protected function setEntryCategory($zoomIntegration, $entry)
	{
		if ($zoomIntegration->getZoomCategory())
		{
			$entry->setCategories($zoomIntegration->getZoomCategory());
		}
	}

	protected function parseAdditionalUsers($additionalUsersZoomResponse, $zoomIntegration)
	{
		$participants = new kZoomParticipants();
		$participants->parseData($additionalUsersZoomResponse);
		$participantsEmails = $participants->getParticipantsEmails();
		if($participantsEmails)
		{
			KalturaLog::debug('Found the following participants: ' . implode(", ", $participantsEmails));
			$result = array();
			foreach ($participantsEmails as $participantEmail)
			{
				$zoomUser = new kZoomUser();
				$zoomUser->setOriginalName($participantEmail);
				$zoomUser->setProcessedName($this->processZoomUserName($participantEmail, $zoomIntegration));
			}
		}
		else
		{
			$result = null;
		}

		return $result;
	}
}