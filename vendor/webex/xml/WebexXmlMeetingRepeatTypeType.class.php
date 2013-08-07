<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingRepeatTypeType extends WebexXmlRequestType
{
	const _WEEKLY = 'WEEKLY';
					
	const _DAILY = 'DAILY';
					
	const _NO_REPEAT = 'NO_REPEAT';
					
	const _CONSTANT = 'CONSTANT';
					
	const _MONTHLY = 'MONTHLY';
					
	const _YEARLY = 'YEARLY';
					
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
		return 'repeatTypeType';
	}
	
}
		
