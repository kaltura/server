<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Render_fibType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiResponse_labelType
	 */
	protected $response_label;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'response_label':
				return 'WebexXmlQtiasiResponse_labelType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'response_label',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'response_label',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'render_fibType';
	}
	
	/**
	 * @param WebexXmlQtiasiResponse_labelType $response_label
	 */
	public function setResponse_label(WebexXmlQtiasiResponse_labelType $response_label)
	{
		$this->response_label = $response_label;
	}
	
	/**
	 * @return WebexXmlQtiasiResponse_labelType $response_label
	 */
	public function getResponse_label()
	{
		return $this->response_label;
	}
	
}
		
