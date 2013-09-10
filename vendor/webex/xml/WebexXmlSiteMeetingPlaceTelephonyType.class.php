<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMeetingPlaceTelephonyType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $persistentTSP;
	
	/**
	 *
	 * @var WebexXmlSiteDirectoryServiceType
	 */
	protected $mpAudioConferencing;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'persistentTSP':
				return 'boolean';
	
			case 'mpAudioConferencing':
				return 'WebexXmlSiteDirectoryServiceType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'persistentTSP',
			'mpAudioConferencing',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'persistentTSP',
			'mpAudioConferencing',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'meetingPlaceTelephonyType';
	}
	
	/**
	 * @param boolean $persistentTSP
	 */
	public function setPersistentTSP($persistentTSP)
	{
		$this->persistentTSP = $persistentTSP;
	}
	
	/**
	 * @return boolean $persistentTSP
	 */
	public function getPersistentTSP()
	{
		return $this->persistentTSP;
	}
	
	/**
	 * @param WebexXmlSiteDirectoryServiceType $mpAudioConferencing
	 */
	public function setMpAudioConferencing(WebexXmlSiteDirectoryServiceType $mpAudioConferencing)
	{
		$this->mpAudioConferencing = $mpAudioConferencing;
	}
	
	/**
	 * @return WebexXmlSiteDirectoryServiceType $mpAudioConferencing
	 */
	public function getMpAudioConferencing()
	{
		return $this->mpAudioConferencing;
	}
	
}
		
