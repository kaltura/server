<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSessionType.class.php');
require_once(__DIR__ . '/WebexXmlSessAccessControlType.class.php');
require_once(__DIR__ . '/WebexXmlSessScheduleType.class.php');

class WebexXmlSessionTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlSessAccessControlType
	 */
	protected $accessControl;
	
	/**
	 *
	 * @var WebexXmlSessScheduleType
	 */
	protected $schedule;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'accessControl',
			'schedule',
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
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'session';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'session:sessionType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSessionType';
	}
	
	/**
	 * @param WebexXmlSessAccessControlType $accessControl
	 */
	public function setAccessControl(WebexXmlSessAccessControlType $accessControl)
	{
		$this->accessControl = $accessControl;
	}
	
	/**
	 * @param WebexXmlSessScheduleType $schedule
	 */
	public function setSchedule(WebexXmlSessScheduleType $schedule)
	{
		$this->schedule = $schedule;
	}
	
}
		
