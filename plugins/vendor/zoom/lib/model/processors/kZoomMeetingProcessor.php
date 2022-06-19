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
	 * @param entry $entry
	 * @throws kCoreException
	 */
	protected function setEntryCategory($entry)
	{
		if ($this->zoomIntegration->getZoomCategory())
		{
			$entry->setCategories($this->zoomIntegration->getZoomCategory());
		}
	}

	protected function parseAdditionalUsers($additionalUsersZoomResponse)
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
				$zoomUser->setProcessedName(kZoomEventHanlder::processZoomUserName($participantEmail, $this->zoomIntegration, $this->zoomClient));
				$result[] = $zoomUser;
			}
		}
		else
		{
			$result = null;
		}

		return $result;
	}
}