<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoom
 */
class kZoomEvent implements iZoomObject
{
	const EVENT = 'event';
	const ACCOUNT_ID = 'account_id';
	const DOWNLOAD_TOKEN = 'download_token';
	const RECORDING_VIDEO_COMPLETED = 'recording.completed';
	const RECORDING_TRANSCRIPT_COMPLETED = 'recording.transcript_completed';

	public $accountId;
	public $eventType;
	public $downloadToken;
	public $object;

	public function parseData($data)
	{
		$this->setEventType($data[self::EVENT]);
		$this->accountId = $data[self::ACCOUNT_ID];
		if(isset($data[self::DOWNLOAD_TOKEN]))
		{
			$this->downloadToken = $data[self::DOWNLOAD_TOKEN];
		}

		$this->parseObject[$data];
	}

	protected function parseObject($data)
	{
		switch ($this->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
				$this->object = new kZoomMeeting();
				$this->object->parseData($data[kZoomMeeting::MEETING_OBJECT]);
				break;
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				break;
			default:
		}
	}

	protected function setEventType($eventName)
	{
		switch ($eventName)
		{
			case self::RECORDING_VIDEO_COMPLETED:
				$this->eventType = kEventType::RECORDING_VIDEO_COMPLETED;
				break;
			case self::RECORDING_TRANSCRIPT_COMPLETED:
				$this->eventType = kEventType::RECORDING_TRANSCRIPT_COMPLETED;
				break;
			default:
				$this->eventType = kEventType::NOT_IMPLEMENTED_EVENT_TYPE;
				KalturaLog::debug('Received zoom event unimplemented event type ' . $eventName);
		}
	}
}