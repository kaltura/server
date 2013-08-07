<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComLanguageType extends WebexXmlRequestType
{
	const _ENGLISH = 'ENGLISH';
					
	const _INTERNATIONAL_ENGLISH = 'INTERNATIONAL ENGLISH';
					
	const _SIMPLIFIED_CHINESE = 'SIMPLIFIED CHINESE';
					
	const _TRADITIONAL_CHINESE = 'TRADITIONAL CHINESE';
					
	const _JAPANESE = 'JAPANESE';
					
	const _KOREAN = 'KOREAN';
					
	const _FRENCH = 'FRENCH';
					
	const _CANADIAN_FRENCH = 'CANADIAN FRENCH';
					
	const _GERMAN = 'GERMAN';
					
	const _ITALIAN = 'ITALIAN';
					
	const _CASTILIAN_SPANISH = 'CASTILIAN SPANISH';
					
	const _LATIN_AMERICAN_SPANISH = 'LATIN AMERICAN SPANISH';
					
	const _SWEDISH = 'SWEDISH';
					
	const _DUTCH = 'DUTCH';
					
	const _BRAZILIAN_PORTUGUESE = 'BRAZILIAN PORTUGUESE';
					
	const _PORTUGUESE = 'PORTUGUESE';
					
	const _SPANISH = 'SPANISH';
					
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
		return 'languageType';
	}
	
}
		
