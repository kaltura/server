<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetWebACDQueues.class.php');
require_once(__DIR__ . '/WebexXmlServWebACDRoleType.class.php');
require_once(__DIR__ . '/WebexXmlEpListControlType.class.php');

class WebexXmlGetWebACDQueuesRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $webExId;
	
	/**
	 *
	 * @var WebexXmlServWebACDRoleType
	 */
	protected $type;
	
	/**
	 *
	 * @var WebexXmlEpListControlType
	 */
	protected $listControl;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExId',
			'type',
			'listControl',
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
		return 'site:GetWebACDQueues';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetWebACDQueues';
	}
	
	/**
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @param WebexXmlServWebACDRoleType $type
	 */
	public function setType(WebexXmlServWebACDRoleType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * @param WebexXmlEpListControlType $listControl
	 */
	public function setListControl(WebexXmlEpListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
}
		
