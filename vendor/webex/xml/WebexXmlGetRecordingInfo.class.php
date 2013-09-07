<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlEpRecordingPlaybackType.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlGetRecordingInfo extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $basic;
	
	/**
	 *
	 * @var WebexXmlEpRecordingPlaybackType
	 */
	protected $playback;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $fileAccess;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'basic':
				return 'WebexXml';
	
			case 'playback':
				return 'WebexXmlEpRecordingPlaybackType';
	
			case 'fileAccess':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXml $basic
	 */
	public function getBasic()
	{
		return $this->basic;
	}
	
	/**
	 * @return WebexXmlEpRecordingPlaybackType $playback
	 */
	public function getPlayback()
	{
		return $this->playback;
	}
	
	/**
	 * @return WebexXml $fileAccess
	 */
	public function getFileAccess()
	{
		return $this->fileAccess;
	}
	
}

