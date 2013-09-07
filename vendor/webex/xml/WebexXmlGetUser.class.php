<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlUseSalesCenterInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlUseSessionTemplateSummaryType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/string.class.php');

class WebexXmlGetUser extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlUseSalesCenterInstanceType
	 */
	protected $salesCenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $peExpired;
	
	/**
	 *
	 * @var boolean
	 */
	protected $peActive;
	
	/**
	 *
	 * @var boolean
	 */
	protected $passwordExpires;
	
	/**
	 *
	 * @var integer
	 */
	protected $passwordDaysLeft;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlUseSessionTemplateSummaryType>
	 */
	protected $schedulingTemplates;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $serviceSessionTypes;
	
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $scheduleFor;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'salesCenter':
				return 'WebexXmlUseSalesCenterInstanceType';
	
			case 'peExpired':
				return 'boolean';
	
			case 'peActive':
				return 'boolean';
	
			case 'passwordExpires':
				return 'boolean';
	
			case 'passwordDaysLeft':
				return 'integer';
	
			case 'schedulingTemplates':
				return 'WebexXmlArray<WebexXmlUseSessionTemplateSummaryType>';
	
			case 'serviceSessionTypes':
				return 'WebexXmlArray<WebexXml>';
	
			case 'scheduleFor':
				return 'WebexXmlArray<string>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlUseSalesCenterInstanceType $salesCenter
	 */
	public function getSalesCenter()
	{
		return $this->salesCenter;
	}
	
	/**
	 * @return boolean $peExpired
	 */
	public function getPeExpired()
	{
		return $this->peExpired;
	}
	
	/**
	 * @return boolean $peActive
	 */
	public function getPeActive()
	{
		return $this->peActive;
	}
	
	/**
	 * @return boolean $passwordExpires
	 */
	public function getPasswordExpires()
	{
		return $this->passwordExpires;
	}
	
	/**
	 * @return integer $passwordDaysLeft
	 */
	public function getPasswordDaysLeft()
	{
		return $this->passwordDaysLeft;
	}
	
	/**
	 * @return WebexXmlArray $schedulingTemplates
	 */
	public function getSchedulingTemplates()
	{
		return $this->schedulingTemplates;
	}
	
	/**
	 * @return WebexXmlArray $serviceSessionTypes
	 */
	public function getServiceSessionTypes()
	{
		return $this->serviceSessionTypes;
	}
	
	/**
	 * @return WebexXmlArray $scheduleFor
	 */
	public function getScheduleFor()
	{
		return $this->scheduleFor;
	}
	
}

