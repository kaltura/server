<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventApprovalConditionType extends WebexXmlRequestType
{
	const _CONTAINS = 'CONTAINS';
					
	const _DOESNOT_CONTAIN = 'DOESNOT_CONTAIN';
					
	const _BEGINS_WITH = 'BEGINS_WITH';
					
	const _ENDS_WITH = 'ENDS_WITH';
					
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
		return 'approvalConditionType';
	}
	
}
		
