<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpEnableOptionsType extends WebexXmlRequestType
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
	protected $poll;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeList;
	
	/**
	 *
	 * @var boolean
	 */
	protected $fileShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $presentation;
	
	/**
	 *
	 * @var boolean
	 */
	protected $applicationShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $desktopShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $webTour;
	
	/**
	 *
	 * @var boolean
	 */
	protected $annotation;
	
	/**
	 *
	 * @var boolean
	 */
	protected $importDocument;
	
	/**
	 *
	 * @var boolean
	 */
	protected $saveDocument;
	
	/**
	 *
	 * @var boolean
	 */
	protected $printDocument;
	
	/**
	 *
	 * @var boolean
	 */
	protected $pointer;
	
	/**
	 *
	 * @var boolean
	 */
	protected $switchPage;
	
	/**
	 *
	 * @var boolean
	 */
	protected $fullScreen;
	
	/**
	 *
	 * @var boolean
	 */
	protected $thumbnail;
	
	/**
	 *
	 * @var boolean
	 */
	protected $zoom;
	
	/**
	 *
	 * @var boolean
	 */
	protected $copyPage;
	
	/**
	 *
	 * @var boolean
	 */
	protected $faxIntoMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $multiVideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $voip;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'chat':
				return 'boolean';
	
			case 'poll':
				return 'boolean';
	
			case 'attendeeList':
				return 'boolean';
	
			case 'fileShare':
				return 'boolean';
	
			case 'presentation':
				return 'boolean';
	
			case 'applicationShare':
				return 'boolean';
	
			case 'desktopShare':
				return 'boolean';
	
			case 'webTour':
				return 'boolean';
	
			case 'annotation':
				return 'boolean';
	
			case 'importDocument':
				return 'boolean';
	
			case 'saveDocument':
				return 'boolean';
	
			case 'printDocument':
				return 'boolean';
	
			case 'pointer':
				return 'boolean';
	
			case 'switchPage':
				return 'boolean';
	
			case 'fullScreen':
				return 'boolean';
	
			case 'thumbnail':
				return 'boolean';
	
			case 'zoom':
				return 'boolean';
	
			case 'copyPage':
				return 'boolean';
	
			case 'faxIntoMeeting':
				return 'boolean';
	
			case 'multiVideo':
				return 'boolean';
	
			case 'voip':
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
			'poll',
			'attendeeList',
			'fileShare',
			'presentation',
			'applicationShare',
			'desktopShare',
			'webTour',
			'annotation',
			'importDocument',
			'saveDocument',
			'printDocument',
			'pointer',
			'switchPage',
			'fullScreen',
			'thumbnail',
			'zoom',
			'copyPage',
			'faxIntoMeeting',
			'multiVideo',
			'voip',
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
		return 'enableOptionsType';
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
	 * @param boolean $poll
	 */
	public function setPoll($poll)
	{
		$this->poll = $poll;
	}
	
	/**
	 * @return boolean $poll
	 */
	public function getPoll()
	{
		return $this->poll;
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
	 * @param boolean $presentation
	 */
	public function setPresentation($presentation)
	{
		$this->presentation = $presentation;
	}
	
	/**
	 * @return boolean $presentation
	 */
	public function getPresentation()
	{
		return $this->presentation;
	}
	
	/**
	 * @param boolean $applicationShare
	 */
	public function setApplicationShare($applicationShare)
	{
		$this->applicationShare = $applicationShare;
	}
	
	/**
	 * @return boolean $applicationShare
	 */
	public function getApplicationShare()
	{
		return $this->applicationShare;
	}
	
	/**
	 * @param boolean $desktopShare
	 */
	public function setDesktopShare($desktopShare)
	{
		$this->desktopShare = $desktopShare;
	}
	
	/**
	 * @return boolean $desktopShare
	 */
	public function getDesktopShare()
	{
		return $this->desktopShare;
	}
	
	/**
	 * @param boolean $webTour
	 */
	public function setWebTour($webTour)
	{
		$this->webTour = $webTour;
	}
	
	/**
	 * @return boolean $webTour
	 */
	public function getWebTour()
	{
		return $this->webTour;
	}
	
	/**
	 * @param boolean $annotation
	 */
	public function setAnnotation($annotation)
	{
		$this->annotation = $annotation;
	}
	
	/**
	 * @return boolean $annotation
	 */
	public function getAnnotation()
	{
		return $this->annotation;
	}
	
	/**
	 * @param boolean $importDocument
	 */
	public function setImportDocument($importDocument)
	{
		$this->importDocument = $importDocument;
	}
	
	/**
	 * @return boolean $importDocument
	 */
	public function getImportDocument()
	{
		return $this->importDocument;
	}
	
	/**
	 * @param boolean $saveDocument
	 */
	public function setSaveDocument($saveDocument)
	{
		$this->saveDocument = $saveDocument;
	}
	
	/**
	 * @return boolean $saveDocument
	 */
	public function getSaveDocument()
	{
		return $this->saveDocument;
	}
	
	/**
	 * @param boolean $printDocument
	 */
	public function setPrintDocument($printDocument)
	{
		$this->printDocument = $printDocument;
	}
	
	/**
	 * @return boolean $printDocument
	 */
	public function getPrintDocument()
	{
		return $this->printDocument;
	}
	
	/**
	 * @param boolean $pointer
	 */
	public function setPointer($pointer)
	{
		$this->pointer = $pointer;
	}
	
	/**
	 * @return boolean $pointer
	 */
	public function getPointer()
	{
		return $this->pointer;
	}
	
	/**
	 * @param boolean $switchPage
	 */
	public function setSwitchPage($switchPage)
	{
		$this->switchPage = $switchPage;
	}
	
	/**
	 * @return boolean $switchPage
	 */
	public function getSwitchPage()
	{
		return $this->switchPage;
	}
	
	/**
	 * @param boolean $fullScreen
	 */
	public function setFullScreen($fullScreen)
	{
		$this->fullScreen = $fullScreen;
	}
	
	/**
	 * @return boolean $fullScreen
	 */
	public function getFullScreen()
	{
		return $this->fullScreen;
	}
	
	/**
	 * @param boolean $thumbnail
	 */
	public function setThumbnail($thumbnail)
	{
		$this->thumbnail = $thumbnail;
	}
	
	/**
	 * @return boolean $thumbnail
	 */
	public function getThumbnail()
	{
		return $this->thumbnail;
	}
	
	/**
	 * @param boolean $zoom
	 */
	public function setZoom($zoom)
	{
		$this->zoom = $zoom;
	}
	
	/**
	 * @return boolean $zoom
	 */
	public function getZoom()
	{
		return $this->zoom;
	}
	
	/**
	 * @param boolean $copyPage
	 */
	public function setCopyPage($copyPage)
	{
		$this->copyPage = $copyPage;
	}
	
	/**
	 * @return boolean $copyPage
	 */
	public function getCopyPage()
	{
		return $this->copyPage;
	}
	
	/**
	 * @param boolean $faxIntoMeeting
	 */
	public function setFaxIntoMeeting($faxIntoMeeting)
	{
		$this->faxIntoMeeting = $faxIntoMeeting;
	}
	
	/**
	 * @return boolean $faxIntoMeeting
	 */
	public function getFaxIntoMeeting()
	{
		return $this->faxIntoMeeting;
	}
	
	/**
	 * @param boolean $multiVideo
	 */
	public function setMultiVideo($multiVideo)
	{
		$this->multiVideo = $multiVideo;
	}
	
	/**
	 * @return boolean $multiVideo
	 */
	public function getMultiVideo()
	{
		return $this->multiVideo;
	}
	
	/**
	 * @param boolean $voip
	 */
	public function setVoip($voip)
	{
		$this->voip = $voip;
	}
	
	/**
	 * @return boolean $voip
	 */
	public function getVoip()
	{
		return $this->voip;
	}
	
}

