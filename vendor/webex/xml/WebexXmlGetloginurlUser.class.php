<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetloginurlUser extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $userLoginURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'userLoginURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $userLoginURL
	 */
	public function getUserLoginURL()
	{
		return $this->userLoginURL;
	}
	
}

