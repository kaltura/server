<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComDayOfWeekType extends WebexXmlRequestType
{
	const _SUNDAY = 'SUNDAY';
					
	const _MONDAY = 'MONDAY';
					
	const _TUESDAY = 'TUESDAY';
					
	const _WEDNESDAY = 'WEDNESDAY';
					
	const _THURSDAY = 'THURSDAY';
					
	const _FRIDAY = 'FRIDAY';
					
	const _SATURDAY = 'SATURDAY';
					
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
		return 'dayOfWeekType';
	}
	
}
		
