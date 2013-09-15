<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComPersonTypeType extends WebexXmlRequestType
{
	const _VISITOR = 'VISITOR';
					
	const _MEMBER = 'MEMBER';
					
	const _PANELIST = 'PANELIST';
					
	const _SME = 'SME';
					
	const _SALESTEAM = 'SALESTEAM';
					
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
		return 'personTypeType';
	}
	
}
		
