<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteScheduleOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $scheduleOnBehalf;
	
	/**
	 *
	 * @var boolean
	 */
	protected $saveSessionTemplate;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'scheduleOnBehalf':
				return 'boolean';
	
			case 'saveSessionTemplate':
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
			'scheduleOnBehalf',
			'saveSessionTemplate',
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
		return 'scheduleOptionsType';
	}
	
	/**
	 * @param boolean $scheduleOnBehalf
	 */
	public function setScheduleOnBehalf($scheduleOnBehalf)
	{
		$this->scheduleOnBehalf = $scheduleOnBehalf;
	}
	
	/**
	 * @return boolean $scheduleOnBehalf
	 */
	public function getScheduleOnBehalf()
	{
		return $this->scheduleOnBehalf;
	}
	
	/**
	 * @param boolean $saveSessionTemplate
	 */
	public function setSaveSessionTemplate($saveSessionTemplate)
	{
		$this->saveSessionTemplate = $saveSessionTemplate;
	}
	
	/**
	 * @return boolean $saveSessionTemplate
	 */
	public function getSaveSessionTemplate()
	{
		return $this->saveSessionTemplate;
	}
	
}
		
