<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlDelRecording extends WebexXmlObject
{
	/**
	 *
	 * @var int
	 */
	protected $recordingID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'recordingID':
				return 'int';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return int $recordingID
	 */
	public function getRecordingID()
	{
		return $this->recordingID;
	}
	
}
		
