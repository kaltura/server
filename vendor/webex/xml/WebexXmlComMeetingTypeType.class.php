<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComMeetingTypeType extends WebexXmlRequestType
{
	const _INITIAL = 'INITIAL';
					
	const _FRE = 'FRE';
					
	const _STD = 'STD';
					
	const _PRO = 'PRO';
					
	const _STANDARD_SUB = 'STANDARD_SUB';
					
	const _PRO_SUB = 'PRO_SUB';
					
	const _PPU = 'PPU';
					
	const _ONCALL = 'ONCALL';
					
	const _ONTOUR = 'ONTOUR';
					
	const _ONSTAGE = 'ONSTAGE';
					
	const _ACCESS_ANYWHERE = 'ACCESS_ANYWHERE';
					
	const _COB = 'COB';
					
	const _OCS = 'OCS';
					
	const _ONS = 'ONS';
					
	const _RAS = 'RAS';
					
	const _SC3 = 'SC3';
					
	const _SOP = 'SOP';
					
	const _SOS = 'SOS';
					
	const _TRS = 'TRS';
					
	const _CUSTOM = 'CUSTOM';
					
	const _SMT = 'SMT';
					
	const _SAC = 'SAC';
					
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
		return 'meetingTypeType';
	}
	
}
		
