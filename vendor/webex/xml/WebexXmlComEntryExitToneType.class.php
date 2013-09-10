<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComEntryExitToneType extends WebexXmlRequestType
{
	const _NOTONE = 'NOTONE';
					
	const _BEEP = 'BEEP';
					
	const _ANNOUNCENAME = 'ANNOUNCENAME';
					
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
		return 'entryExitToneType';
	}
	
}
		
