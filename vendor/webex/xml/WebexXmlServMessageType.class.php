<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServMessageType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $header;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlServBodyContentType>
	 */
	protected $body;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'header':
				return 'WebexXml';
	
			case 'body':
				return 'WebexXmlArray<WebexXmlServBodyContentType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'header',
			'body',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'header',
			'body',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'messageType';
	}
	
	/**
	 * @param WebexXml $header
	 */
	public function setHeader(WebexXml $header)
	{
		$this->header = $header;
	}
	
	/**
	 * @return WebexXml $header
	 */
	public function getHeader()
	{
		return $this->header;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlServBodyContentType> $body
	 */
	public function setBody(WebexXmlArray $body)
	{
		if($body->getType() != 'WebexXmlServBodyContentType')
			throw new WebexXmlException(get_class($this) . "::body must be of type WebexXmlServBodyContentType");
		
		$this->body = $body;
	}
	
	/**
	 * @return WebexXmlArray $body
	 */
	public function getBody()
	{
		return $this->body;
	}
	
}
		
