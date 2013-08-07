<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2ItemfeedbackType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiFlow_matType
	 */
	protected $flow_mat;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'flow_mat':
				return 'WebexXmlQtiasiFlow_matType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'flow_mat',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'flow_mat',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'itemfeedbackType';
	}
	
	/**
	 * @param WebexXmlQtiasiFlow_matType $flow_mat
	 */
	public function setFlow_mat(WebexXmlQtiasiFlow_matType $flow_mat)
	{
		$this->flow_mat = $flow_mat;
	}
	
	/**
	 * @return WebexXmlQtiasiFlow_matType $flow_mat
	 */
	public function getFlow_mat()
	{
		return $this->flow_mat;
	}
	
}
		
