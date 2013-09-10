<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

abstract class WebexXmlRequestBodyContent extends WebexXmlRequestType
{
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getAttributes()
	 */
	protected function getAttributes()
	{
		return array(
			'xsi:type' => $this->getRequestType(),
			'xmlns:' . $this->getServiceType() => 'http://www.webex.com/schemas/2002/06/service/' . $this->getServiceType(),
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'bodyContent';
	}
	
	/**
	 * @return string
	 */
	abstract protected function getServiceType();
	
	/**
	 * @return string
	 */
	abstract protected function getRequestType();
	
	/**
	 * @return string
	 */
	abstract public function getContentType();
}
