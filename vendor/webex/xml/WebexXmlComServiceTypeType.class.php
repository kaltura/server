<?php
require_once(__DIR__ . '/WebexXmlEnumerator.class.php');

class WebexXmlComServiceTypeType extends WebexXmlEnumerator
{
	const _MEETINGCENTER = 'MeetingCenter';
					
	const _EVENTCENTER = 'EventCenter';
					
	const _TRAININGCENTER = 'TrainingCenter';
					
	const _SUPPORTCENTER = 'SupportCenter';
					
	const _SALESCENTER = 'SalesCenter';
					
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
		return 'serviceType';
	}
	
}
		
