<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpSessionRegisterStatusType extends WebexXmlRequestType
{
	const _FULL = 'FULL';
					
	const _CLOSED = 'CLOSED';
					
	const _WAITLIST = 'WAITLIST';
					
	const _REGISTER = 'REGISTER';
					
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
		return 'sessionRegisterStatusType';
	}
	
}
		
