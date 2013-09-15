<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlQtiasiQuestestinteropType.class.php');

class WebexXmlGetIMStestDetails extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlQtiasiQuestestinteropType
	 */
	protected $questestinterop;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'questestinterop':
				return 'WebexXmlQtiasiQuestestinteropType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlQtiasiQuestestinteropType $questestinterop
	 */
	public function getQuestestinterop()
	{
		return $this->questestinterop;
	}
	
}
		
