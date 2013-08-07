<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComJoinStatusType extends WebexXmlRequestType
{
	const _REGISTER = 'REGISTER';
					
	const _INVITE = 'INVITE';
					
	const _REJECT = 'REJECT';
					
	const _ACCEPT = 'ACCEPT';
					
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
		return 'joinStatusType';
	}
	
}
		
