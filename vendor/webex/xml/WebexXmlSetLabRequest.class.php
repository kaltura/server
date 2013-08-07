<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlSetLab.class.php');
require_once(__DIR__ . '/WebexXmlTrainLabType.class.php');

class WebexXmlSetLabRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $labID;
	
	/**
	 *
	 * @var WebexXmlTrainLabType
	 */
	protected $lab;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'labID',
			'lab',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'labID',
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
		return 'trainingsession:setLab';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlSetLab';
	}
	
	/**
	 * @param long $labID
	 */
	public function setLabID($labID)
	{
		$this->labID = $labID;
	}
	
	/**
	 * @param WebexXmlTrainLabType $lab
	 */
	public function setLab(WebexXmlTrainLabType $lab)
	{
		$this->lab = $lab;
	}
	
}
		
