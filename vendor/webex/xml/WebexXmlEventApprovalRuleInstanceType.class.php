<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventApprovalRuleInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $enrollField;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enrollField':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'enrollField',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'enrollField',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'approvalRuleInstanceType';
	}
	
	/**
	 * @param string $enrollField
	 */
	public function setEnrollField($enrollField)
	{
		$this->enrollField = $enrollField;
	}
	
	/**
	 * @return string $enrollField
	 */
	public function getEnrollField()
	{
		return $this->enrollField;
	}
	
}

