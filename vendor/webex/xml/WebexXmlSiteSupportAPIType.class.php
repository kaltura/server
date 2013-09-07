<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteSupportAPIType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $autoLogin;
	
	/**
	 *
	 * @var boolean
	 */
	protected $aspAndPHPAPI;
	
	/**
	 *
	 * @var boolean
	 */
	protected $backwardAPI;
	
	/**
	 *
	 * @var boolean
	 */
	protected $xmlAPI;
	
	/**
	 *
	 * @var boolean
	 */
	protected $cAPI;
	
	/**
	 *
	 * @var boolean
	 */
	protected $scorm;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'autoLogin':
				return 'boolean';
	
			case 'aspAndPHPAPI':
				return 'boolean';
	
			case 'backwardAPI':
				return 'boolean';
	
			case 'xmlAPI':
				return 'boolean';
	
			case 'cAPI':
				return 'boolean';
	
			case 'scorm':
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
			'autoLogin',
			'aspAndPHPAPI',
			'backwardAPI',
			'xmlAPI',
			'cAPI',
			'scorm',
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
		return 'supportAPIType';
	}
	
	/**
	 * @param boolean $autoLogin
	 */
	public function setAutoLogin($autoLogin)
	{
		$this->autoLogin = $autoLogin;
	}
	
	/**
	 * @return boolean $autoLogin
	 */
	public function getAutoLogin()
	{
		return $this->autoLogin;
	}
	
	/**
	 * @param boolean $aspAndPHPAPI
	 */
	public function setAspAndPHPAPI($aspAndPHPAPI)
	{
		$this->aspAndPHPAPI = $aspAndPHPAPI;
	}
	
	/**
	 * @return boolean $aspAndPHPAPI
	 */
	public function getAspAndPHPAPI()
	{
		return $this->aspAndPHPAPI;
	}
	
	/**
	 * @param boolean $backwardAPI
	 */
	public function setBackwardAPI($backwardAPI)
	{
		$this->backwardAPI = $backwardAPI;
	}
	
	/**
	 * @return boolean $backwardAPI
	 */
	public function getBackwardAPI()
	{
		return $this->backwardAPI;
	}
	
	/**
	 * @param boolean $xmlAPI
	 */
	public function setXmlAPI($xmlAPI)
	{
		$this->xmlAPI = $xmlAPI;
	}
	
	/**
	 * @return boolean $xmlAPI
	 */
	public function getXmlAPI()
	{
		return $this->xmlAPI;
	}
	
	/**
	 * @param boolean $cAPI
	 */
	public function setCAPI($cAPI)
	{
		$this->cAPI = $cAPI;
	}
	
	/**
	 * @return boolean $cAPI
	 */
	public function getCAPI()
	{
		return $this->cAPI;
	}
	
	/**
	 * @param boolean $scorm
	 */
	public function setScorm($scorm)
	{
		$this->scorm = $scorm;
	}
	
	/**
	 * @return boolean $scorm
	 */
	public function getScorm()
	{
		return $this->scorm;
	}
	
}
		
