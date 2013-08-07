<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlEpDistListInstanceType.class.php');

class WebexXmlListDistList extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEpDistListInstanceType>
	 */
	protected $distList;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'distList':
				return 'WebexXmlArray<WebexXmlEpDistListInstanceType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $distList
	 */
	public function getDistList()
	{
		return $this->distList;
	}
	
}
		
