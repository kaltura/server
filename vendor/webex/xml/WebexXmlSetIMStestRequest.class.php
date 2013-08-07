<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetIMStest.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlQtiasiQuestestinteropType.class.php');

class WebexXmlSetIMStestRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $testID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $display;
	
	/**
	 *
	 * @var boolean
	 */
	protected $assignGrades;
	
	/**
	 *
	 * @var WebexXmlQtiasiQuestestinteropType
	 */
	protected $questestinterop;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
			'display',
			'assignGrades',
			'questestinterop',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
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
		return 'trainingsession:setIMStest';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetIMStest';
	}
	
	/**
	 * @param long $testID
	 */
	public function setTestID($testID)
	{
		$this->testID = $testID;
	}
	
	/**
	 * @param WebexXml $display
	 */
	public function setDisplay(WebexXml $display)
	{
		$this->display = $display;
	}
	
	/**
	 * @param boolean $assignGrades
	 */
	public function setAssignGrades($assignGrades)
	{
		$this->assignGrades = $assignGrades;
	}
	
	/**
	 * @param WebexXmlQtiasiQuestestinteropType $questestinterop
	 */
	public function setQuestestinterop(WebexXmlQtiasiQuestestinteropType $questestinterop)
	{
		$this->questestinterop = $questestinterop;
	}
	
}
		
