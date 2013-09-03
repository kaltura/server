<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpRecordingFileAccessType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $registration;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeView;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeDownload;
	
	/**
	 *
	 * @var string
	 */
	protected $endPlayURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'registration':
				return 'boolean';
	
			case 'attendeeView':
				return 'boolean';
	
			case 'attendeeDownload':
				return 'boolean';
	
			case 'endPlayURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'registration',
			'attendeeView',
			'attendeeDownload',
			'endPlayURL',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordingFileAccessType';
	}
	
	/**
	 * @param boolean $registration
	 */
	public function setRegistration($registration)
	{
		$this->registration = $registration;
	}
	
	/**
	 * @return boolean $registration
	 */
	public function getRegistration()
	{
		return $this->registration;
	}
	
	/**
	 * @param boolean $attendeeView
	 */
	public function setAttendeeView($attendeeView)
	{
		$this->attendeeView = $attendeeView;
	}
	
	/**
	 * @return boolean $attendeeView
	 */
	public function getAttendeeView()
	{
		return $this->attendeeView;
	}
	
	/**
	 * @param boolean $attendeeDownload
	 */
	public function setAttendeeDownload($attendeeDownload)
	{
		$this->attendeeDownload = $attendeeDownload;
	}
	
	/**
	 * @return boolean $attendeeDownload
	 */
	public function getAttendeeDownload()
	{
		return $this->attendeeDownload;
	}
	
	/**
	 * @param string $endPlayURL
	 */
	public function setEndPlayURL($endPlayURL)
	{
		$this->endPlayURL = $endPlayURL;
	}
	
	/**
	 * @return string $endPlayURL
	 */
	public function getEndPlayURL()
	{
		return $this->endPlayURL;
	}
	
}
		
