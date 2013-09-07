<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventApprovalRuleType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $enrollFieldID;
	
	/**
	 *
	 * @var WebexXmlEventApprovalConditionType
	 */
	protected $condition;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $queryField;
	
	/**
	 *
	 * @var WebexXmlEventApprovalActionType
	 */
	protected $action;
	
	/**
	 *
	 * @var boolean
	 */
	protected $matchCase;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enrollFieldID':
				return 'long';
	
			case 'condition':
				return 'WebexXmlEventApprovalConditionType';
	
			case 'queryField':
				return 'WebexXml';
	
			case 'action':
				return 'WebexXmlEventApprovalActionType';
	
			case 'matchCase':
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
			'enrollFieldID',
			'condition',
			'queryField',
			'action',
			'matchCase',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'enrollFieldID',
			'condition',
			'queryField',
			'action',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'approvalRuleType';
	}
	
	/**
	 * @param long $enrollFieldID
	 */
	public function setEnrollFieldID($enrollFieldID)
	{
		$this->enrollFieldID = $enrollFieldID;
	}
	
	/**
	 * @return long $enrollFieldID
	 */
	public function getEnrollFieldID()
	{
		return $this->enrollFieldID;
	}
	
	/**
	 * @param WebexXmlEventApprovalConditionType $condition
	 */
	public function setCondition(WebexXmlEventApprovalConditionType $condition)
	{
		$this->condition = $condition;
	}
	
	/**
	 * @return WebexXmlEventApprovalConditionType $condition
	 */
	public function getCondition()
	{
		return $this->condition;
	}
	
	/**
	 * @param WebexXml $queryField
	 */
	public function setQueryField(WebexXml $queryField)
	{
		$this->queryField = $queryField;
	}
	
	/**
	 * @return WebexXml $queryField
	 */
	public function getQueryField()
	{
		return $this->queryField;
	}
	
	/**
	 * @param WebexXmlEventApprovalActionType $action
	 */
	public function setAction(WebexXmlEventApprovalActionType $action)
	{
		$this->action = $action;
	}
	
	/**
	 * @return WebexXmlEventApprovalActionType $action
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	/**
	 * @param boolean $matchCase
	 */
	public function setMatchCase($matchCase)
	{
		$this->matchCase = $matchCase;
	}
	
	/**
	 * @return boolean $matchCase
	 */
	public function getMatchCase()
	{
		return $this->matchCase;
	}
	
}
		
