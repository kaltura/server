<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlResponseBody extends WebexXmlObject
{
	/**
	 * @var WebexXmlResponseBodyContent
	 */
	protected $bodyContent;
	
	/**
	 * @var string
	 */
	protected $contentType;
	
	public function __construct(SimpleXMLElement $xml, $contentType)
	{
		$this->contentType = $contentType;
		
		parent::__construct($xml);
	}
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'bodyContent':
				return $this->contentType;
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlResponseBodyContent $bodyContent
	 */
	public function getBodyContent()
	{
		return $this->bodyContent;
	}
}
