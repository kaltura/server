<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteOneClickType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $allowJoinUnlistMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $requireApproveJoin;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'allowJoinUnlistMeeting':
				return 'boolean';
	
			case 'requireApproveJoin':
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
			'allowJoinUnlistMeeting',
			'requireApproveJoin',
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
		return 'oneClickType';
	}
	
	/**
	 * @param boolean $allowJoinUnlistMeeting
	 */
	public function setAllowJoinUnlistMeeting($allowJoinUnlistMeeting)
	{
		$this->allowJoinUnlistMeeting = $allowJoinUnlistMeeting;
	}
	
	/**
	 * @return boolean $allowJoinUnlistMeeting
	 */
	public function getAllowJoinUnlistMeeting()
	{
		return $this->allowJoinUnlistMeeting;
	}
	
	/**
	 * @param boolean $requireApproveJoin
	 */
	public function setRequireApproveJoin($requireApproveJoin)
	{
		$this->requireApproveJoin = $requireApproveJoin;
	}
	
	/**
	 * @return boolean $requireApproveJoin
	 */
	public function getRequireApproveJoin()
	{
		return $this->requireApproveJoin;
	}
	
}
		
