<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseScRequestType extends WebexXmlRequestType
{
	const _DESK_VIEW = 'DESK_VIEW';
					
	const _DESK_CTRL = 'DESK_CTRL';
					
	const _APP_VIEW = 'APP_VIEW';
					
	const _APP_CTRL = 'APP_CTRL';
					
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
		return 'scRequestType';
	}
	
}
		
