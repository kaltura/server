<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetRecordingInfo.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlEpRecordingBasicType.class.php');
require_once(__DIR__ . '/WebexXmlEpRecordingPlaybackType.class.php');
require_once(__DIR__ . '/WebexXmlEpRecordingFileAccessType.class.php');

class WebexXmlSetRecordingInfoRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $recording;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isServiceRecording;
	
	/**
	 *
	 * @var WebexXmlEpRecordingBasicType
	 */
	protected $basic;
	
	/**
	 *
	 * @var WebexXmlEpRecordingPlaybackType
	 */
	protected $playback;
	
	/**
	 *
	 * @var WebexXmlEpRecordingFileAccessType
	 */
	protected $fileAccess;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'recording',
			'isServiceRecording',
			'basic',
			'playback',
			'fileAccess',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'recording',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:setRecordingInfo';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetRecordingInfo';
	}
	
	/**
	 * @param WebexXml $recording
	 */
	public function setRecording(WebexXml $recording)
	{
		$this->recording = $recording;
	}
	
	/**
	 * @param boolean $isServiceRecording
	 */
	public function setIsServiceRecording($isServiceRecording)
	{
		$this->isServiceRecording = $isServiceRecording;
	}
	
	/**
	 * @param WebexXmlEpRecordingBasicType $basic
	 */
	public function setBasic(WebexXmlEpRecordingBasicType $basic)
	{
		$this->basic = $basic;
	}
	
	/**
	 * @param WebexXmlEpRecordingPlaybackType $playback
	 */
	public function setPlayback(WebexXmlEpRecordingPlaybackType $playback)
	{
		$this->playback = $playback;
	}
	
	/**
	 * @param WebexXmlEpRecordingFileAccessType $fileAccess
	 */
	public function setFileAccess(WebexXmlEpRecordingFileAccessType $fileAccess)
	{
		$this->fileAccess = $fileAccess;
	}
	
}
		
