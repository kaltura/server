<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeOrderByType extends WebexXmlRequestType
{
	const _CONFID = 'CONFID';
					
	const _ATTENDEEID = 'ATTENDEEID';
					
	const _ATTENDEETYPE = 'ATTENDEETYPE';
					
	const _ATTENDEENAME = 'ATTENDEENAME';
					
	const _ATTENDEEWEBEXID = 'ATTENDEEWEBEXID';
					
	const _JOINSTATUS = 'JOINSTATUS';
					
	const _EMAIL = 'EMAIL';
					
	const _PHONE = 'PHONE';
					
	const _MOBILE = 'MOBILE';
					
	const _FAX = 'FAX';
					
	const _COMPANY = 'COMPANY';
					
	const _TITLE = 'TITLE';
					
	const _URL = 'URL';
					
	const _ADDRESS1 = 'ADDRESS1';
					
	const _ADDRESS2 = 'ADDRESS2';
					
	const _CITY = 'CITY';
					
	const _STATE = 'STATE';
					
	const _ZIPCODE = 'ZIPCODE';
					
	const _COUNTRY = 'COUNTRY';
					
	const _NOTES = 'NOTES';
					
	const _ADDRESSTYPE = 'ADDRESSTYPE';
					
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
		
