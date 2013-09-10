<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetSite.class.php');
require_once(__DIR__ . '/WebexXmlSiteMeetingPlaceType.class.php');

class WebexXmlSetSiteRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlSiteMeetingPlaceType
	 */
	protected $meetingPlace;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingPlace',
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
		return 'site';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'site:setSite';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetSite';
	}
	
	/**
	 * @param WebexXmlSiteMeetingPlaceType $meetingPlace
	 */
	public function setMeetingPlace(WebexXmlSiteMeetingPlaceType $meetingPlace)
	{
		$this->meetingPlace = $meetingPlace;
	}
	
}
		
