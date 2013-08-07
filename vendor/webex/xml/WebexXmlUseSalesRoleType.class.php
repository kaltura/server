<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSalesRoleType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $rep;
	
	/**
	 *
	 * @var boolean
	 */
	protected $mgr;
	
	/**
	 *
	 * @var boolean
	 */
	protected $asst;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sme;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'rep':
				return 'boolean';
	
			case 'mgr':
				return 'boolean';
	
			case 'asst':
				return 'boolean';
	
			case 'sme':
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
			'rep',
			'mgr',
			'asst',
			'sme',
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
		return 'salesRoleType';
	}
	
	/**
	 * @param boolean $rep
	 */
	public function setRep($rep)
	{
		$this->rep = $rep;
	}
	
	/**
	 * @return boolean $rep
	 */
	public function getRep()
	{
		return $this->rep;
	}
	
	/**
	 * @param boolean $mgr
	 */
	public function setMgr($mgr)
	{
		$this->mgr = $mgr;
	}
	
	/**
	 * @return boolean $mgr
	 */
	public function getMgr()
	{
		return $this->mgr;
	}
	
	/**
	 * @param boolean $asst
	 */
	public function setAsst($asst)
	{
		$this->asst = $asst;
	}
	
	/**
	 * @return boolean $asst
	 */
	public function getAsst()
	{
		return $this->asst;
	}
	
	/**
	 * @param boolean $sme
	 */
	public function setSme($sme)
	{
		$this->sme = $sme;
	}
	
	/**
	 * @return boolean $sme
	 */
	public function getSme()
	{
		return $this->sme;
	}
	
}
		
