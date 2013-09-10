<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEnrollmentType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $idReq;
	
	/**
	 *
	 * @var boolean
	 */
	protected $passwordReq;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $password;
	
	/**
	 *
	 * @var boolean
	 */
	protected $approvalReq;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventApprovalRuleType>
	 */
	protected $approvalRules;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $endURLAfterEnroll;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'idReq':
				return 'boolean';
	
			case 'passwordReq':
				return 'boolean';
	
			case 'password':
				return 'WebexXml';
	
			case 'approvalReq':
				return 'boolean';
	
			case 'approvalRules':
				return 'WebexXmlArray<WebexXmlEventApprovalRuleType>';
	
			case 'endURLAfterEnroll':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'idReq',
			'passwordReq',
			'password',
			'approvalReq',
			'approvalRules',
			'endURLAfterEnroll',
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
		return 'enrollmentType';
	}
	
	/**
	 * @param boolean $idReq
	 */
	public function setIdReq($idReq)
	{
		$this->idReq = $idReq;
	}
	
	/**
	 * @return boolean $idReq
	 */
	public function getIdReq()
	{
		return $this->idReq;
	}
	
	/**
	 * @param boolean $passwordReq
	 */
	public function setPasswordReq($passwordReq)
	{
		$this->passwordReq = $passwordReq;
	}
	
	/**
	 * @return boolean $passwordReq
	 */
	public function getPasswordReq()
	{
		return $this->passwordReq;
	}
	
	/**
	 * @param WebexXml $password
	 */
	public function setPassword(WebexXml $password)
	{
		$this->password = $password;
	}
	
	/**
	 * @return WebexXml $password
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @param boolean $approvalReq
	 */
	public function setApprovalReq($approvalReq)
	{
		$this->approvalReq = $approvalReq;
	}
	
	/**
	 * @return boolean $approvalReq
	 */
	public function getApprovalReq()
	{
		return $this->approvalReq;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventApprovalRuleType> $approvalRules
	 */
	public function setApprovalRules(WebexXmlArray $approvalRules)
	{
		if($approvalRules->getType() != 'WebexXmlEventApprovalRuleType')
			throw new WebexXmlException(get_class($this) . "::approvalRules must be of type WebexXmlEventApprovalRuleType");
		
		$this->approvalRules = $approvalRules;
	}
	
	/**
	 * @return WebexXmlArray $approvalRules
	 */
	public function getApprovalRules()
	{
		return $this->approvalRules;
	}
	
	/**
	 * @param WebexXml $endURLAfterEnroll
	 */
	public function setEndURLAfterEnroll(WebexXml $endURLAfterEnroll)
	{
		$this->endURLAfterEnroll = $endURLAfterEnroll;
	}
	
	/**
	 * @return WebexXml $endURLAfterEnroll
	 */
	public function getEndURLAfterEnroll()
	{
		return $this->endURLAfterEnroll;
	}
	
}
		
