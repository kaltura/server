<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/long.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlCreateTrainingSession extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $sessionkey;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $additionalInfo;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionkey':
				return 'WebexXmlArray<long>';
	
			case 'additionalInfo':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $sessionkey
	 */
	public function getSessionkey()
	{
		return $this->sessionkey;
	}
	
	/**
	 * @return WebexXml $additionalInfo
	 */
	public function getAdditionalInfo()
	{
		return $this->additionalInfo;
	}
	
}

