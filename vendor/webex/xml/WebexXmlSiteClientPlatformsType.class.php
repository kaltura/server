<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteClientPlatformsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $msWindows;
	
	/**
	 *
	 * @var boolean
	 */
	protected $macOS9;
	
	/**
	 *
	 * @var boolean
	 */
	protected $macOSX;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sunSolaris;
	
	/**
	 *
	 * @var boolean
	 */
	protected $linux;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hpUnix;
	
	/**
	 *
	 * @var boolean
	 */
	protected $java;
	
	/**
	 *
	 * @var boolean
	 */
	protected $palm;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'msWindows':
				return 'boolean';
	
			case 'macOS9':
				return 'boolean';
	
			case 'macOSX':
				return 'boolean';
	
			case 'sunSolaris':
				return 'boolean';
	
			case 'linux':
				return 'boolean';
	
			case 'hpUnix':
				return 'boolean';
	
			case 'java':
				return 'boolean';
	
			case 'palm':
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
			'msWindows',
			'macOS9',
			'macOSX',
			'sunSolaris',
			'linux',
			'hpUnix',
			'java',
			'palm',
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
		return 'clientPlatformsType';
	}
	
	/**
	 * @param boolean $msWindows
	 */
	public function setMsWindows($msWindows)
	{
		$this->msWindows = $msWindows;
	}
	
	/**
	 * @return boolean $msWindows
	 */
	public function getMsWindows()
	{
		return $this->msWindows;
	}
	
	/**
	 * @param boolean $macOS9
	 */
	public function setMacOS9($macOS9)
	{
		$this->macOS9 = $macOS9;
	}
	
	/**
	 * @return boolean $macOS9
	 */
	public function getMacOS9()
	{
		return $this->macOS9;
	}
	
	/**
	 * @param boolean $macOSX
	 */
	public function setMacOSX($macOSX)
	{
		$this->macOSX = $macOSX;
	}
	
	/**
	 * @return boolean $macOSX
	 */
	public function getMacOSX()
	{
		return $this->macOSX;
	}
	
	/**
	 * @param boolean $sunSolaris
	 */
	public function setSunSolaris($sunSolaris)
	{
		$this->sunSolaris = $sunSolaris;
	}
	
	/**
	 * @return boolean $sunSolaris
	 */
	public function getSunSolaris()
	{
		return $this->sunSolaris;
	}
	
	/**
	 * @param boolean $linux
	 */
	public function setLinux($linux)
	{
		$this->linux = $linux;
	}
	
	/**
	 * @return boolean $linux
	 */
	public function getLinux()
	{
		return $this->linux;
	}
	
	/**
	 * @param boolean $hpUnix
	 */
	public function setHpUnix($hpUnix)
	{
		$this->hpUnix = $hpUnix;
	}
	
	/**
	 * @return boolean $hpUnix
	 */
	public function getHpUnix()
	{
		return $this->hpUnix;
	}
	
	/**
	 * @param boolean $java
	 */
	public function setJava($java)
	{
		$this->java = $java;
	}
	
	/**
	 * @return boolean $java
	 */
	public function getJava()
	{
		return $this->java;
	}
	
	/**
	 * @param boolean $palm
	 */
	public function setPalm($palm)
	{
		$this->palm = $palm;
	}
	
	/**
	 * @return boolean $palm
	 */
	public function getPalm()
	{
		return $this->palm;
	}
	
}
		
