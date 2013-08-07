<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpRecordingPlaybackType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $chat;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportQandA;
	
	/**
	 *
	 * @var boolean
	 */
	protected $video;
	
	/**
	 *
	 * @var boolean
	 */
	protected $polling;
	
	/**
	 *
	 * @var boolean
	 */
	protected $notes;
	
	/**
	 *
	 * @var boolean
	 */
	protected $fileShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeList;
	
	/**
	 *
	 * @var boolean
	 */
	protected $toc;
	
	/**
	 *
	 * @var WebexXmlEpPlaybackRangeType
	 */
	protected $range;
	
	/**
	 *
	 * @var long
	 */
	protected $partialStart;
	
	/**
	 *
	 * @var long
	 */
	protected $partialEnd;
	
	/**
	 *
	 * @var boolean
	 */
	protected $includeNBRcontrols;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'chat':
				return 'boolean';
	
			case 'supportQandA':
				return 'boolean';
	
			case 'video':
				return 'boolean';
	
			case 'polling':
				return 'boolean';
	
			case 'notes':
				return 'boolean';
	
			case 'fileShare':
				return 'boolean';
	
			case 'attendeeList':
				return 'boolean';
	
			case 'toc':
				return 'boolean';
	
			case 'range':
				return 'WebexXmlEpPlaybackRangeType';
	
			case 'partialStart':
				return 'long';
	
			case 'partialEnd':
				return 'long';
	
			case 'includeNBRcontrols':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'chat',
			'supportQandA',
			'video',
			'polling',
			'notes',
			'fileShare',
			'attendeeList',
			'toc',
			'range',
			'partialStart',
			'partialEnd',
			'includeNBRcontrols',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordingPlaybackType';
	}
	
	/**
	 * @param boolean $chat
	 */
	public function setChat($chat)
	{
		$this->chat = $chat;
	}
	
	/**
	 * @return boolean $chat
	 */
	public function getChat()
	{
		return $this->chat;
	}
	
	/**
	 * @param boolean $supportQandA
	 */
	public function setSupportQandA($supportQandA)
	{
		$this->supportQandA = $supportQandA;
	}
	
	/**
	 * @return boolean $supportQandA
	 */
	public function getSupportQandA()
	{
		return $this->supportQandA;
	}
	
	/**
	 * @param boolean $video
	 */
	public function setVideo($video)
	{
		$this->video = $video;
	}
	
	/**
	 * @return boolean $video
	 */
	public function getVideo()
	{
		return $this->video;
	}
	
	/**
	 * @param boolean $polling
	 */
	public function setPolling($polling)
	{
		$this->polling = $polling;
	}
	
	/**
	 * @return boolean $polling
	 */
	public function getPolling()
	{
		return $this->polling;
	}
	
	/**
	 * @param boolean $notes
	 */
	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	
	/**
	 * @return boolean $notes
	 */
	public function getNotes()
	{
		return $this->notes;
	}
	
	/**
	 * @param boolean $fileShare
	 */
	public function setFileShare($fileShare)
	{
		$this->fileShare = $fileShare;
	}
	
	/**
	 * @return boolean $fileShare
	 */
	public function getFileShare()
	{
		return $this->fileShare;
	}
	
	/**
	 * @param boolean $attendeeList
	 */
	public function setAttendeeList($attendeeList)
	{
		$this->attendeeList = $attendeeList;
	}
	
	/**
	 * @return boolean $attendeeList
	 */
	public function getAttendeeList()
	{
		return $this->attendeeList;
	}
	
	/**
	 * @param boolean $toc
	 */
	public function setToc($toc)
	{
		$this->toc = $toc;
	}
	
	/**
	 * @return boolean $toc
	 */
	public function getToc()
	{
		return $this->toc;
	}
	
	/**
	 * @param WebexXmlEpPlaybackRangeType $range
	 */
	public function setRange(WebexXmlEpPlaybackRangeType $range)
	{
		$this->range = $range;
	}
	
	/**
	 * @return WebexXmlEpPlaybackRangeType $range
	 */
	public function getRange()
	{
		return $this->range;
	}
	
	/**
	 * @param long $partialStart
	 */
	public function setPartialStart($partialStart)
	{
		$this->partialStart = $partialStart;
	}
	
	/**
	 * @return long $partialStart
	 */
	public function getPartialStart()
	{
		return $this->partialStart;
	}
	
	/**
	 * @param long $partialEnd
	 */
	public function setPartialEnd($partialEnd)
	{
		$this->partialEnd = $partialEnd;
	}
	
	/**
	 * @return long $partialEnd
	 */
	public function getPartialEnd()
	{
		return $this->partialEnd;
	}
	
	/**
	 * @param boolean $includeNBRcontrols
	 */
	public function setIncludeNBRcontrols($includeNBRcontrols)
	{
		$this->includeNBRcontrols = $includeNBRcontrols;
	}
	
	/**
	 * @return boolean $includeNBRcontrols
	 */
	public function getIncludeNBRcontrols()
	{
		return $this->includeNBRcontrols;
	}
	
}
		
