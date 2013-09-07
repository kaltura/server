<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServMeetingAssistType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlServAssistRequestType
	 */
	protected $assistRequest;
	
	/**
	 *
	 * @var WebexXmlServAssistConfirmedType
	 */
	protected $assistConfirm;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'assistRequest':
				return 'WebexXmlServAssistRequestType';
	
			case 'assistConfirm':
				return 'WebexXmlServAssistConfirmedType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'assistRequest',
			'assistConfirm',
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
		return 'meetingAssistType';
	}
	
	/**
	 * @param WebexXmlServAssistRequestType $assistRequest
	 */
	public function setAssistRequest(WebexXmlServAssistRequestType $assistRequest)
	{
		$this->assistRequest = $assistRequest;
	}
	
	/**
	 * @return WebexXmlServAssistRequestType $assistRequest
	 */
	public function getAssistRequest()
	{
		return $this->assistRequest;
	}
	
	/**
	 * @param WebexXmlServAssistConfirmedType $assistConfirm
	 */
	public function setAssistConfirm(WebexXmlServAssistConfirmedType $assistConfirm)
	{
		$this->assistConfirm = $assistConfirm;
	}
	
	/**
	 * @return WebexXmlServAssistConfirmedType $assistConfirm
	 */
	public function getAssistConfirm()
	{
		return $this->assistConfirm;
	}
	
}
		
