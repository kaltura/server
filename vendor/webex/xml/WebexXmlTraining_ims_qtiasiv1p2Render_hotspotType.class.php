<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Render_hotspotType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiResponse_naType
	 */
	protected $response_na;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'response_na':
				return 'WebexXmlQtiasiResponse_naType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'response_na',
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
		return 'render_hotspotType';
	}
	
	/**
	 * @param WebexXmlQtiasiResponse_naType $response_na
	 */
	public function setResponse_na(WebexXmlQtiasiResponse_naType $response_na)
	{
		$this->response_na = $response_na;
	}
	
	/**
	 * @return WebexXmlQtiasiResponse_naType $response_na
	 */
	public function getResponse_na()
	{
		return $this->response_na;
	}
	
}
		
