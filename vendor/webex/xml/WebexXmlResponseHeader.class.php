<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlHeaderResponse.class.php');

class WebexXmlResponseHeader extends WebexXmlObject
{
	/**
	 * @var WebexXmlHeaderResponse
	 */
	protected $response;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'response':
				return 'WebexXmlHeaderResponse';
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlHeaderResponse $response
	 */
	public function getResponse()
	{
		return $this->response;
	}
}
