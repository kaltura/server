<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteRegionType extends WebexXmlRequestType
{
	const _U_S_ = 'U.S.';
					
	const _AUSTRALIA = 'Australia';
					
	const _CANADA = 'Canada';
					
	const _FRENCH_CANADA = 'French Canada';
					
	const _CHINA = 'China';
					
	const _GERMANY = 'Germany';
					
	const _HONG_KONG = 'Hong Kong';
					
	const _ITALY = 'Italy';
					
	const _JAPAN = 'Japan';
					
	const _KOREA = 'Korea';
					
	const _NEW_ZEALAND = 'New Zealand';
					
	const _SPAIN = 'Spain';
					
	const _SWEDEN = 'Sweden';
					
	const _SWITZERLAND = 'Switzerland';
					
	const _TAIWAN = 'Taiwan';
					
	const _U_K_ = 'U.K.';
					
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
		return 'regionType';
	}
	
}
		
