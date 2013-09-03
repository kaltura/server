<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');

class WebexXmlRequestBody
{
	/**
	 * @var WebexXmlRequestBodyContent
	 */
	protected $bodyContent;
	
	public function __construct(WebexXmlRequestBodyContent $bodyContent)
	{
		$this->bodyContent = $bodyContent;
	}
	
	public function __toString()
	{
		return "<body>$this->bodyContent</body>";
	}
}
