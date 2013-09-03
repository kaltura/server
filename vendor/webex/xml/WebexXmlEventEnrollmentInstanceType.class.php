<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEnrollmentInstanceType extends WebexXmlRequestType
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
	 * @var string
	 */
	protected $password;
	
	/**
	 *
	 * @var boolean
	 */
	protected $approvalReq;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventApprovalRuleInstanceType>
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
				return 'string';
	
			case 'approvalReq':
				return 'boolean';
	
			case 'approvalRules':
				return 'WebexXmlArray<WebexXmlEventApprovalRuleInstanceType>';
	
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
		return 'enrollmentInstanceType';
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
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	/**
	 * @return string $password
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
	 * @param WebexXmlArray<WebexXmlEventApprovalRuleInstanceType> $approvalRules
	 */
	public function setApprovalRules(WebexXmlArray $approvalRules)
	{
		if($approvalRules->getType() != 'WebexXmlEventApprovalRuleInstanceType')
			throw new WebexXmlException(get_class($this) . "::approvalRules must be of type WebexXmlEventApprovalRuleInstanceType");
		
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
		
