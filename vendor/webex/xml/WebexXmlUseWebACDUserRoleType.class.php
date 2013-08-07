<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseWebACDUserRoleType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $isAgent;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isMgr;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'isAgent':
				return 'boolean';
	
			case 'isMgr':
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
			'isAgent',
			'isMgr',
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
		return 'webACDUserRoleType';
	}
	
	/**
	 * @param boolean $isAgent
	 */
	public function setIsAgent($isAgent)
	{
		$this->isAgent = $isAgent;
	}
	
	/**
	 * @return boolean $isAgent
	 */
	public function getIsAgent()
	{
		return $this->isAgent;
	}
	
	/**
	 * @param boolean $isMgr
	 */
	public function setIsMgr($isMgr)
	{
		$this->isMgr = $isMgr;
	}
	
	/**
	 * @return boolean $isMgr
	 */
	public function getIsMgr()
	{
		return $this->isMgr;
	}
	
}
		
