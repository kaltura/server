<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainAvailabilityLabType.class.php');

class WebexXmlCheckLabAvailability extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainAvailabilityLabType>
	 */
	protected $availabilityLabs;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'availabilityLabs':
				return 'WebexXmlArray<WebexXmlTrainAvailabilityLabType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $availabilityLabs
	 */
	public function getAvailabilityLabs()
	{
		return $this->availabilityLabs;
	}
	
}

