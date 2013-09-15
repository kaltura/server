<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingOrderByType extends WebexXmlRequestType
{
	const _HOSTWEBEXID = 'HOSTWEBEXID';
					
	const _CONFNAME = 'CONFNAME';
					
	const _STARTTIME = 'STARTTIME';
					
	const _TRACKINGCODE1 = 'TRACKINGCODE1';
					
	const _TRACKINGCODE2 = 'TRACKINGCODE2';
					
	const _TRACKINGCODE3 = 'TRACKINGCODE3';
					
	const _TRACKINGCODE4 = 'TRACKINGCODE4';
					
	const _TRACKINGCODE5 = 'TRACKINGCODE5';
					
	const _TRACKINGCODE6 = 'TRACKINGCODE6';
					
	const _TRACKINGCODE7 = 'TRACKINGCODE7';
					
	const _TRACKINGCODE8 = 'TRACKINGCODE8';
					
	const _TRACKINGCODE9 = 'TRACKINGCODE9';
					
	const _TRACKINGCODE10 = 'TRACKINGCODE10';
					
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
		return 'orderByType';
	}
	
}
