<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlCreateLab.class.php');
require_once(__DIR__ . '/WebexXmlTrainLabType.class.php');

class WebexXmlCreateLabRequest extends WebexXmlRequestBodyContent
{
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
			'lab',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'lab',
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
		return 'trainingsession:createLab';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlCreateLab';
	}
	
	/**
	 * @param WebexXmlTrainLabType $lab
	 */
	public function setLab(WebexXmlTrainLabType $lab)
	{
		$this->lab = $lab;
	}
	
}
		
