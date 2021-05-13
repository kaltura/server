<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomMeetingProcessor extends zoomRecordingProcessor
{
	protected function getAdditionalUsersFromZoom($recordingId)
	{
		return $this->zoomClient->retrieveMeetingParticipant($recordingId);
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
		if ($this->dropFolder->zoomVendorIntegration->zoomCategory)
		{
			$categories[] = $this->dropFolder->zoomVendorIntegration->zoomCategory;
		}
		if ($categories)
		{
			$entry->categories = implode(',', $categories);
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
				$zoomUser->setProcessedName($this->processZoomUserName($participantEmail));
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