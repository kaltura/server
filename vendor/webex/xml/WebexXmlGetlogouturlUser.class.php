<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetlogouturlUser extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $userLogoutURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'userLogoutURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $userLogoutURL
	 */
	public function getUserLogoutURL()
	{
		return $this->userLogoutURL;
	}
	
}

