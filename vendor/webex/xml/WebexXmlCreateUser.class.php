<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlCreateUser extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $userId;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'userId':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}
	
}

