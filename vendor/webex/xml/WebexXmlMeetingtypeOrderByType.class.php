<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingtypeOrderByType extends WebexXmlRequestType
{
	const _MEETINGTYPEID = 'MEETINGTYPEID';
					
	const _PRODUCTCODEPREFIX = 'PRODUCTCODEPREFIX';
					
	const _MEETINGTYPENAME = 'MEETINGTYPENAME';
					
	const _MEETINGTYPEDISPLAYNAME = 'MEETINGTYPEDISPLAYNAME';
					
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
