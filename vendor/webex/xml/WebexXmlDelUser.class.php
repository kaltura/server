<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlUseDelUserStatusType.class.php');

class WebexXmlDelUser extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlUseDelUserStatusType>
	 */
	protected $user;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'user':
				return 'WebexXmlArray<WebexXmlUseDelUserStatusType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $user
	 */
	public function getUser()
	{
		return $this->user;
	}
	
}

