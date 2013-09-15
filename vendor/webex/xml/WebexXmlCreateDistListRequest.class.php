<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlCreateDistList.class.php');
require_once(__DIR__ . '/WebexXmlEpDistListWithContactType.class.php');

class WebexXmlCreateDistListRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlEpDistListWithContactType
	 */
	protected $distList;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'distList',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'distList',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:createDistList';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlCreateDistList';
	}
	
	/**
	 * @param WebexXmlEpDistListWithContactType $distList
	 */
	public function setDistList(WebexXmlEpDistListWithContactType $distList)
	{
		$this->distList = $distList;
	}
	
}
		
