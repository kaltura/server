<?php
require_once(__DIR__ . '/WebexXmlRequestHeader.class.php');
require_once(__DIR__ . '/WebexXmlRequestBody.class.php');
require_once(__DIR__ . '/WebexXmlSecurityContext.class.php');
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');

class WebexXmlRequest
{
	/**
	 * @var WebexXmlRequestHeader
	 */
	protected $header;

	/**
	 * @var WebexXmlRequestBody
	 */
	protected $body;

	/**
	 * @var string
	 */
	protected $contentType;

	public function __construct(WebexXmlSecurityContext $securityContext, WebexXmlRequestBodyContent $bodyContent)
	{
		$this->header = new WebexXmlRequestHeader($securityContext);
		$this->body = new WebexXmlRequestBody($bodyContent);
		$this->contentType = $bodyContent->getContentType();
	}

	public function __toString()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:serv="http://www.webex.com/schemas/2002/06/service">';
		$xml .= $this->header;
		$xml .= $this->body;
		$xml .= '</serv:message>';

		return $xml;
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}