<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetSalesSession.class.php');

class WebexXmlGetSalesSessionRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $meetingKey;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'meetingKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'sales';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'sales:getSalesSession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetSalesSession';
	}
	
	/**
	 * @param long $meetingKey
	 */
	public function setMeetingKey($meetingKey)
	{
		$this->meetingKey = $meetingKey;
	}
	
}
		
