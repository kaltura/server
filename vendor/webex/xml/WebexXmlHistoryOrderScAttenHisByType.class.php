<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryOrderScAttenHisByType extends WebexXmlRequestType
{
	const _ATTENDEENAME = 'ATTENDEENAME';
					
	const _STARTTIME = 'STARTTIME';
					
	const _SESSIONKEY = 'SESSIONKEY';
					
	const _CONFID = 'CONFID';
					
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
		return 'orderScAttenHisByType';
	}
	
}
		
