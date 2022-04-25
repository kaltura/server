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
		$categoryTrackingField = $this->zoomClient->retrieveTrackingField($meetingId);
		KBatchBase::impersonate($this->dropFolder->partnerId);
		if ($categoryTrackingField)
		{
			$this->addEntryToCategory($categoryTrackingField, $entry->id);
		}
		if ($this->dropFolder->zoomVendorIntegration->zoomCategory)
		{
			$this->addEntryToCategory($this->dropFolder->zoomVendorIntegration->zoomCategory, $entry->id);
		}
		KBatchBase::unimpersonate();
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
				$zoomUser->setProcessedName(ZoomBatchUtils::processZoomUserName($participantEmail, $this->dropFolder->zoomVendorIntegration, $this->zoomClient));
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