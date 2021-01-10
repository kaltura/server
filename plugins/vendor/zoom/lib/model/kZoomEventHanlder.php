<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomEventHanlder
{
	const PHP_INPUT = 'php://input';
	protected $zoomConfiguration;

	/**
	 * kZoomEngine constructor.
	 * @param $zoomConfiguration
	 */
	public function __construct($zoomConfiguration)
	{
		$this->zoomConfiguration = $zoomConfiguration;
	}

	/**
	 * @return kZoomEvent
	 * @throws Exception
	 */
	public function parseEvent()
	{
		kZoomOauth::verifyHeaderToken($this->zoomConfiguration);
		$data = $this->getRequestData();
		KalturaLog::debug('Zoom event data is ' . print_r($data, true));
		$event = new kZoomEvent();
		$event->parseData($data);
		return $event;
	}

	/**
	 * @param kZoomEvent $event
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function processEvent($event)
	{
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				KalturaLog::notice('This is an old Zoom event type - Not processing');
				break;
			case kEventType::NEW_RECORDING_VIDEO_COMPLETED:
				/* @var kZoomRecording $recording */
				$recording = $event->object;
				$zoomBaseUrl = $this->zoomConfiguration[kZoomClient::ZOOM_BASE_URL];
				if($recording->recordingType == kRecordingType::WEBINAR)
				{
					$zoomRecordingProcessor = new kZoomWebinarProcessor($zoomBaseUrl);
				}
				else
				{
					$zoomRecordingProcessor = new kZoomMeetingProcessor($zoomBaseUrl);
				}

				$zoomRecordingProcessor->handleRecordingVideoComplete($event);
				break;
			case kEventType::NEW_RECORDING_TRANSCRIPT_COMPLETED:
				$transcriptProcessor = new kZoomTranscriptProcessor($this->zoomConfiguration[kZoomClient::ZOOM_BASE_URL]);
				$transcriptProcessor->handleRecordingTranscriptComplete($event);
				break;
		}
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	protected function getRequestData()
	{
		$request_body = file_get_contents(self::PHP_INPUT);
		return json_decode($request_body, true);
	}
}