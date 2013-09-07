<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseOrderByType extends WebexXmlRequestType
{
	const _UID = 'UID';
					
	const _WEBEXID = 'WEBEXID';
					
	const _FIRSTNAME = 'FIRSTNAME';
					
	const _LASTNAME = 'LASTNAME';
					
	const _EMAIL = 'EMAIL';
					
	const _ACTIVE = 'ACTIVE';
					
	const _REGDATE = 'REGDATE';
					
	const _REGEXPDATE = 'REGEXPDATE';
					
	const _PRIVILEGE = 'PRIVILEGE';
					
	const _PERSONALURL = 'PERSONALURL';
					
	const _ADDRESS1 = 'ADDRESS1';
					
	const _ADDRESS2 = 'ADDRESS2';
					
	const _CITY = 'CITY';
					
	const _STATE = 'STATE';
					
	const _ZIPCODE = 'ZIPCODE';
					
	const _COUNTRY = 'COUNTRY';
					
	const _PHONE1 = 'PHONE1';
					
	const _PHONE2 = 'PHONE2';
					
	const _MOBILE1 = 'MOBILE1';
					
	const _MOBILE2 = 'MOBILE2';
					
	const _FAX = 'FAX';
					
	const _PAGER = 'PAGER';
					
	const _EMAIL2 = 'EMAIL2';
					
	const _DIVISION = 'DIVISION';
					
	const _DEPARTMENT = 'DEPARTMENT';
					
	const _PROJECT = 'PROJECT';
					
	const _OTHER = 'OTHER';
					
	const _TIMEZONE = 'TIMEZONE';
					
	const _OFFICENAME = 'OFFICENAME';
					
	const _OFFICETITLE = 'OFFICETITLE';
					
	const _OFFICEURL = 'OFFICEURL';
					
	const _CATEGORYID = 'CATEGORYID';
					
	const _VISITCOUNT = 'VISITCOUNT';
					
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
		
