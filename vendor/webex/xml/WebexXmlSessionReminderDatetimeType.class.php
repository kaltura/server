<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSessionReminderDatetimeType extends WebexXmlRequestType
{
	const _15MIN = '15MIN';
					
	const _30MIN = '30MIN';
					
	const _1HR = '1HR';
					
	const _2HR = '2HR';
					
	const _24HR = '24HR';
					
	const _2DAY = '2DAY';
					
	const _7DAY = '7DAY';
					
	const _14DAY = '14DAY';
					
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
		return 'reminderDatetimeType';
	}
	
}
