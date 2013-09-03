<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2ResponseType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiResponse_formType
	 */
	protected $response_form;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiResponse_valueType>
	 */
	protected $response_value;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'response_form':
				return 'WebexXmlQtiResponse_formType';
	
			case 'response_value':
				return 'WebexXmlArray<WebexXmlQtiResponse_valueType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'response_form',
			'response_value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'response_form',
			'response_value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'responseType';
	}
	
	/**
	 * @param WebexXmlQtiResponse_formType $response_form
	 */
	public function setResponse_form(WebexXmlQtiResponse_formType $response_form)
	{
		$this->response_form = $response_form;
	}
	
	/**
	 * @return WebexXmlQtiResponse_formType $response_form
	 */
	public function getResponse_form()
	{
		return $this->response_form;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiResponse_valueType> $response_value
	 */
	public function setResponse_value(WebexXmlArray $response_value)
	{
		if($response_value->getType() != 'WebexXmlQtiResponse_valueType')
			throw new WebexXmlException(get_class($this) . "::response_value must be of type WebexXmlQtiResponse_valueType");
		
		$this->response_value = $response_value;
	}
	
	/**
	 * @return WebexXmlArray $response_value
	 */
	public function getResponse_value()
	{
		return $this->response_value;
	}
	
}
		
