<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetIMStestResult.class.php');

class WebexXmlGetIMStestResultRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var integer
	 */
	protected $testID;
	
	/**
	 *
	 * @var string
	 */
	protected $participantEmail;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
			'participantEmail',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
			'participantEmail',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'trainingsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'trainingsession:getIMStestResult';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetIMStestResult';
	}
	
	/**
	 * @param integer $testID
	 */
	public function setTestID($testID)
	{
		$this->testID = $testID;
	}
	
	/**
	 * @param string $participantEmail
	 */
	public function setParticipantEmail($participantEmail)
	{
		$this->participantEmail = $participantEmail;
	}
	
}
		
