<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlUseUserSummaryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListSummaryUser extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlUseUserSummaryInstanceType>
	 */
	protected $user;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'user':
				return 'WebexXmlArray<WebexXmlUseUserSummaryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
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
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}

