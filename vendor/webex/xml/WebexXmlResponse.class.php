<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlResponseHeader.class.php');
require_once(__DIR__ . '/WebexXmlResponseBody.class.php');

class WebexXmlResponse extends WebexXmlObject
{
	/**
	 * @var WebexXmlResponseHeader
	 */
	protected $header;
	
	/**
	 * @var WebexXmlResponseBody
	 */
	protected $body;

	public function __construct(SimpleXMLElement $xml, $contentType)
	{	
		$this->body = new WebexXmlResponseBody($xml->body, $contentType);
		
		parent::__construct($xml);
	}
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'header':
				return 'WebexXmlResponseHeader';
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlResponseHeader $header
	 */
	public function getHeader()
	{
		return $this->header;
	}

	/**
	 * @return WebexXmlResponseBody $body
	 */
	public function getBody()
	{
		return $this->body;
	}
}
