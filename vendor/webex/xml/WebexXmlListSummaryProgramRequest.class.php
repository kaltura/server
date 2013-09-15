<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListSummaryProgram.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlEventProgramOrderType.class.php');

class WebexXmlListSummaryProgramRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlEventProgramOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var long
	 */
	protected $programID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'listControl',
			'order',
			'programID',
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
		return 'event';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'event:lstsummaryProgram';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListSummaryProgram';
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlEventProgramOrderType $order
	 */
	public function setOrder(WebexXmlEventProgramOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param long $programID
	 */
	public function setProgramID($programID)
	{
		$this->programID = $programID;
	}
	
}
		
