<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomParticipants implements iZoomObject
{
	const PARTICIPANTS = 'participants';

	public $participants;

	public function parseData($data, $role = null)
	{
		$this->participants = array();
		if(isset($data[self::PARTICIPANTS]))
		{
			foreach ($data[self::PARTICIPANTS] as $participantData)
			{
				$participant = new kZoomParticipant();
				$participant->parseData($participantData, $role);
				$this->participants[] = $participant;
			}
		}
	}

	public function getParticipantsEmails()
	{
		$emails = array();
		if($this->participants)
		{
			foreach ($this->participants as $participant)
			{
				/* @var kZoomParticipant $participant */
				$emails[] = $participant->email;
			}
		}

		return $emails;
	}
}
