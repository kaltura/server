<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteResourceRestrictionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $isLicenseManager;
	
	/**
	 *
	 * @var long
	 */
	protected $concurrentLicense;
	
	/**
	 *
	 * @var long
	 */
	protected $fileFolderCapacity;
	
	/**
	 *
	 * @var long
	 */
	protected $maxConcurrentEvents;
	
	/**
	 *
	 * @var long
	 */
	protected $archiveStorageLimit;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'isLicenseManager':
				return 'boolean';
	
			case 'concurrentLicense':
				return 'long';
	
			case 'fileFolderCapacity':
				return 'long';
	
			case 'maxConcurrentEvents':
				return 'long';
	
			case 'archiveStorageLimit':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'isLicenseManager',
			'concurrentLicense',
			'fileFolderCapacity',
			'maxConcurrentEvents',
			'archiveStorageLimit',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'isLicenseManager',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'resourceRestrictionsType';
	}
	
	/**
	 * @param boolean $isLicenseManager
	 */
	public function setIsLicenseManager($isLicenseManager)
	{
		$this->isLicenseManager = $isLicenseManager;
	}
	
	/**
	 * @return boolean $isLicenseManager
	 */
	public function getIsLicenseManager()
	{
		return $this->isLicenseManager;
	}
	
	/**
	 * @param long $concurrentLicense
	 */
	public function setConcurrentLicense($concurrentLicense)
	{
		$this->concurrentLicense = $concurrentLicense;
	}
	
	/**
	 * @return long $concurrentLicense
	 */
	public function getConcurrentLicense()
	{
		return $this->concurrentLicense;
	}
	
	/**
	 * @param long $fileFolderCapacity
	 */
	public function setFileFolderCapacity($fileFolderCapacity)
	{
		$this->fileFolderCapacity = $fileFolderCapacity;
	}
	
	/**
	 * @return long $fileFolderCapacity
	 */
	public function getFileFolderCapacity()
	{
		return $this->fileFolderCapacity;
	}
	
	/**
	 * @param long $maxConcurrentEvents
	 */
	public function setMaxConcurrentEvents($maxConcurrentEvents)
	{
		$this->maxConcurrentEvents = $maxConcurrentEvents;
	}
	
	/**
	 * @return long $maxConcurrentEvents
	 */
	public function getMaxConcurrentEvents()
	{
		return $this->maxConcurrentEvents;
	}
	
	/**
	 * @param long $archiveStorageLimit
	 */
	public function setArchiveStorageLimit($archiveStorageLimit)
	{
		$this->archiveStorageLimit = $archiveStorageLimit;
	}
	
	/**
	 * @return long $archiveStorageLimit
	 */
	public function getArchiveStorageLimit()
	{
		return $this->archiveStorageLimit;
	}
	
}
		
