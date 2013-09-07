<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteProductivityToolType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $enable;
	
	/**
	 *
	 * @var WebexXmlSiteInstallationOptionType
	 */
	protected $installOpts;
	
	/**
	 *
	 * @var WebexXmlSiteIntegrationType
	 */
	protected $integrations;
	
	/**
	 *
	 * @var WebexXmlSiteOneClickType
	 */
	protected $oneClick;
	
	/**
	 *
	 * @var WebexXmlSiteTemplateType
	 */
	protected $templates;
	
	/**
	 *
	 * @var WebexXmlSiteLockDownPTType
	 */
	protected $lockDownPT;
	
	/**
	 *
	 * @var WebexXmlSiteImSettingsType
	 */
	protected $imSettings;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enable':
				return 'boolean';
	
			case 'installOpts':
				return 'WebexXmlSiteInstallationOptionType';
	
			case 'integrations':
				return 'WebexXmlSiteIntegrationType';
	
			case 'oneClick':
				return 'WebexXmlSiteOneClickType';
	
			case 'templates':
				return 'WebexXmlSiteTemplateType';
	
			case 'lockDownPT':
				return 'WebexXmlSiteLockDownPTType';
	
			case 'imSettings':
				return 'WebexXmlSiteImSettingsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'enable',
			'installOpts',
			'integrations',
			'oneClick',
			'templates',
			'lockDownPT',
			'imSettings',
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
		return 'productivityToolType';
	}
	
	/**
	 * @param boolean $enable
	 */
	public function setEnable($enable)
	{
		$this->enable = $enable;
	}
	
	/**
	 * @return boolean $enable
	 */
	public function getEnable()
	{
		return $this->enable;
	}
	
	/**
	 * @param WebexXmlSiteInstallationOptionType $installOpts
	 */
	public function setInstallOpts(WebexXmlSiteInstallationOptionType $installOpts)
	{
		$this->installOpts = $installOpts;
	}
	
	/**
	 * @return WebexXmlSiteInstallationOptionType $installOpts
	 */
	public function getInstallOpts()
	{
		return $this->installOpts;
	}
	
	/**
	 * @param WebexXmlSiteIntegrationType $integrations
	 */
	public function setIntegrations(WebexXmlSiteIntegrationType $integrations)
	{
		$this->integrations = $integrations;
	}
	
	/**
	 * @return WebexXmlSiteIntegrationType $integrations
	 */
	public function getIntegrations()
	{
		return $this->integrations;
	}
	
	/**
	 * @param WebexXmlSiteOneClickType $oneClick
	 */
	public function setOneClick(WebexXmlSiteOneClickType $oneClick)
	{
		$this->oneClick = $oneClick;
	}
	
	/**
	 * @return WebexXmlSiteOneClickType $oneClick
	 */
	public function getOneClick()
	{
		return $this->oneClick;
	}
	
	/**
	 * @param WebexXmlSiteTemplateType $templates
	 */
	public function setTemplates(WebexXmlSiteTemplateType $templates)
	{
		$this->templates = $templates;
	}
	
	/**
	 * @return WebexXmlSiteTemplateType $templates
	 */
	public function getTemplates()
	{
		return $this->templates;
	}
	
	/**
	 * @param WebexXmlSiteLockDownPTType $lockDownPT
	 */
	public function setLockDownPT(WebexXmlSiteLockDownPTType $lockDownPT)
	{
		$this->lockDownPT = $lockDownPT;
	}
	
	/**
	 * @return WebexXmlSiteLockDownPTType $lockDownPT
	 */
	public function getLockDownPT()
	{
		return $this->lockDownPT;
	}
	
	/**
	 * @param WebexXmlSiteImSettingsType $imSettings
	 */
	public function setImSettings(WebexXmlSiteImSettingsType $imSettings)
	{
		$this->imSettings = $imSettings;
	}
	
	/**
	 * @return WebexXmlSiteImSettingsType $imSettings
	 */
	public function getImSettings()
	{
		return $this->imSettings;
	}
	
}
		
