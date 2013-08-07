<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Response_formType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiCorrect_responseType>
	 */
	protected $correct_response;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'correct_response':
				return 'WebexXmlArray<WebexXmlQtiCorrect_responseType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'correct_response',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'correct_response',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'response_formType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiCorrect_responseType> $correct_response
	 */
	public function setCorrect_response(WebexXmlArray $correct_response)
	{
		if($correct_response->getType() != 'WebexXmlQtiCorrect_responseType')
			throw new WebexXmlException(get_class($this) . "::correct_response must be of type WebexXmlQtiCorrect_responseType");
		
		$this->correct_response = $correct_response;
	}
	
	/**
	 * @return WebexXmlArray $correct_response
	 */
	public function getCorrect_response()
	{
		return $this->correct_response;
	}
	
}
		
