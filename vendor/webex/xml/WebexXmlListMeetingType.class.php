<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlMtgtypeMeetingTypeInstanceType.class.php');

class WebexXmlListMeetingType extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlMtgtypeMeetingTypeInstanceType>
	 */
	protected $meetingType;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
			case 'meetingType':
				return 'WebexXmlArray<WebexXmlMtgtypeMeetingTypeInstanceType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
	/**
	 * @return WebexXmlArray $meetingType
	 */
	public function getMeetingType()
	{
		return $this->meetingType;
	}
	
}

