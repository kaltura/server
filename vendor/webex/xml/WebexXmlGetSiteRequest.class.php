<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetSite.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlGetSiteRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $returnSettings;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'returnSettings',
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
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'site';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'site:getSite';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetSite';
	}
	
	/**
	 * @param WebexXml $returnSettings
	 */
	public function setReturnSettings(WebexXml $returnSettings)
	{
		$this->returnSettings = $returnSettings;
	}
	
}
		
