<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlSiteSiteType.class.php');

class WebexXmlGetSite extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlSiteSiteType
	 */
	protected $siteInstance;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'siteInstance':
				return 'WebexXmlSiteSiteType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlSiteSiteType $siteInstance
	 */
	public function getSiteInstance()
	{
		return $this->siteInstance;
	}
	
}

