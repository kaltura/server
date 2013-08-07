<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComOsType extends WebexXmlRequestType
{
	const _WIN2K = 'WIN2K';
					
	const _WINNT = 'WINNT';
					
	const _WIN9X = 'WIN9X';
					
	const _LINUX = 'LINUX';
					
	const _HPUX = 'HPUX';
					
	const _AIX = 'AIX';
					
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
		return 'osType';
	}
	
}
		
