<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2PresentationType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiFlowType
	 */
	protected $flow;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'flow':
				return 'WebexXmlQtiasiFlowType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'flow',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'flow',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'presentationType';
	}
	
	/**
	 * @param WebexXmlQtiasiFlowType $flow
	 */
	public function setFlow(WebexXmlQtiasiFlowType $flow)
	{
		$this->flow = $flow;
	}
	
	/**
	 * @return WebexXmlQtiasiFlowType $flow
	 */
	public function getFlow()
	{
		return $this->flow;
	}
	
}
		
