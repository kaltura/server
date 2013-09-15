<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventSurveyDisplayType extends WebexXmlRequestType
{
	const _NO_DISPLAY = 'NO_DISPLAY';
					
	const _POPUP_WINDOW = 'POPUP_WINDOW';
					
	const _MAIN_WINDOW = 'MAIN_WINDOW';
					
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
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
		return 'surveyDisplayType';
	}
	
}
