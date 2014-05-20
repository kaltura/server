<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelRecording.class.php');

class WebexXmlDelRecordingRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var int
	 */
	protected $recordingID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isServiceRecording;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'recordingID',
			'isServiceRecording',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'recordingID',
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
		return 'ep:delRecording';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelRecording';
	}
	
	/**
	 * @param int $recordingID
	 */
	public function setRecordingID($recordingID)
	{
		$this->recordingID = $recordingID;
	}
	
	/**
	 * @param boolean $isServiceRecording
	 */
	public function setIsServiceRecording($isServiceRecording)
	{
		$this->isServiceRecording = $isServiceRecording;
	}
	
}
		
