<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventPostEventSurveyType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventSurveyDisplayType
	 */
	protected $display;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'display':
				return 'WebexXmlEventSurveyDisplayType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'display',
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
		return 'postEventSurveyType';
	}
	
	/**
	 * @param WebexXmlEventSurveyDisplayType $display
	 */
	public function setDisplay(WebexXmlEventSurveyDisplayType $display)
	{
		$this->display = $display;
	}
	
	/**
	 * @return WebexXmlEventSurveyDisplayType $display
	 */
	public function getDisplay()
	{
		return $this->display;
	}
	
}
		
