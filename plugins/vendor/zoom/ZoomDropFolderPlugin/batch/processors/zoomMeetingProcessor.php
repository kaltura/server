<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomMeetingProcessor extends zoomRecordingProcessor
{
	protected function getAdditionalUsersFromZoom($recordingId)
	{
		return $this->zoomClient->retrieveReportMeetingParticipant($recordingId);
	}

	protected function getAlternativeHostsData($recordingId)
	{
		return $this->zoomClient->retrieveMeeting($recordingId);
	}

	protected function getCoHostsData($recordingId, $pageSize, $nextPageToken)
	{
		return $this->zoomClient->retrieveMetricsMeetingParticipant($recordingId, $pageSize, $nextPageToken);
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
		return parent::parseZoomEmails($participantsEmails);
	}
}