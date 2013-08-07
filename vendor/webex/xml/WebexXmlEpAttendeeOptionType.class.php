<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpAttendeeOptionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $approvalBeforeJoin;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'approvalBeforeJoin':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'approvalBeforeJoin',
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
		return 'attendeeOptionType';
	}
	
	/**
	 * @param boolean $approvalBeforeJoin
	 */
	public function setApprovalBeforeJoin($approvalBeforeJoin)
	{
		$this->approvalBeforeJoin = $approvalBeforeJoin;
	}
	
	/**
	 * @return boolean $approvalBeforeJoin
	 */
	public function getApprovalBeforeJoin()
	{
		return $this->approvalBeforeJoin;
	}
	
}
		
