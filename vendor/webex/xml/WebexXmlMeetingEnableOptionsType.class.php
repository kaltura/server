<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingEnableOptionsType extends WebexXmlRequestType
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
	protected $audioVideo;
	
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
	protected $meetingRecord;
	
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
	protected $rcAppShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $rcDesktopShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $rcWebTour;
	
	/**
	 *
	 * @var boolean
	 */
	protected $javaClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $nativeClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeRecordMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $voip;
	
	/**
	 *
	 * @var boolean
	 */
	protected $faxIntoMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enableReg;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportQandA;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportFeedback;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportBreakoutSessions;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPanelists;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportRemoteComputer;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportShareWebContent;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportUCFWebPages;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportUCFRichMedia;
	
	/**
	 *
	 * @var boolean
	 */
	protected $autoDeleteAfterMeetingEnd;
	
	/**
	 *
	 * @var boolean
	 */
	protected $viewAnyDoc;
	
	/**
	 *
	 * @var boolean
	 */
	protected $viewAnyPage;
	
	/**
	 *
	 * @var boolean
	 */
	protected $allowContactPrivate;
	
	/**
	 *
	 * @var boolean
	 */
	protected $chatHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $chatPresenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $chatAllAttendees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $multiVideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $notes;
	
	/**
	 *
	 * @var boolean
	 */
	protected $closedCaptions;
	
	/**
	 *
	 * @var boolean
	 */
	protected $singleNote;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendFeedback;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayQuickStartHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayQuickStartAttendees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportE2E;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPKI;
	
	/**
	 *
	 * @var boolean
	 */
	protected $HQvideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $HDvideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $viewVideoThumbs;
	
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
	
			case 'audioVideo':
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
	
			case 'meetingRecord':
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
	
			case 'rcAppShare':
				return 'boolean';
	
			case 'rcDesktopShare':
				return 'boolean';
	
			case 'rcWebTour':
				return 'boolean';
	
			case 'javaClient':
				return 'boolean';
	
			case 'nativeClient':
				return 'boolean';
	
			case 'attendeeRecordMeeting':
				return 'boolean';
	
			case 'voip':
				return 'boolean';
	
			case 'faxIntoMeeting':
				return 'boolean';
	
			case 'enableReg':
				return 'boolean';
	
			case 'supportQandA':
				return 'boolean';
	
			case 'supportFeedback':
				return 'boolean';
	
			case 'supportBreakoutSessions':
				return 'boolean';
	
			case 'supportPanelists':
				return 'boolean';
	
			case 'supportRemoteComputer':
				return 'boolean';
	
			case 'supportShareWebContent':
				return 'boolean';
	
			case 'supportUCFWebPages':
				return 'boolean';
	
			case 'supportUCFRichMedia':
				return 'boolean';
	
			case 'autoDeleteAfterMeetingEnd':
				return 'boolean';
	
			case 'viewAnyDoc':
				return 'boolean';
	
			case 'viewAnyPage':
				return 'boolean';
	
			case 'allowContactPrivate':
				return 'boolean';
	
			case 'chatHost':
				return 'boolean';
	
			case 'chatPresenter':
				return 'boolean';
	
			case 'chatAllAttendees':
				return 'boolean';
	
			case 'multiVideo':
				return 'boolean';
	
			case 'notes':
				return 'boolean';
	
			case 'closedCaptions':
				return 'boolean';
	
			case 'singleNote':
				return 'boolean';
	
			case 'sendFeedback':
				return 'boolean';
	
			case 'displayQuickStartHost':
				return 'boolean';
	
			case 'displayQuickStartAttendees':
				return 'boolean';
	
			case 'supportE2E':
				return 'boolean';
	
			case 'supportPKI':
				return 'boolean';
	
			case 'HQvideo':
				return 'boolean';
	
			case 'HDvideo':
				return 'boolean';
	
			case 'viewVideoThumbs':
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
			'audioVideo',
			'attendeeList',
			'fileShare',
			'presentation',
			'applicationShare',
			'desktopShare',
			'webTour',
			'meetingRecord',
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
			'rcAppShare',
			'rcDesktopShare',
			'rcWebTour',
			'javaClient',
			'nativeClient',
			'attendeeRecordMeeting',
			'voip',
			'faxIntoMeeting',
			'enableReg',
			'supportQandA',
			'supportFeedback',
			'supportBreakoutSessions',
			'supportPanelists',
			'supportRemoteComputer',
			'supportShareWebContent',
			'supportUCFWebPages',
			'supportUCFRichMedia',
			'autoDeleteAfterMeetingEnd',
			'viewAnyDoc',
			'viewAnyPage',
			'allowContactPrivate',
			'chatHost',
			'chatPresenter',
			'chatAllAttendees',
			'multiVideo',
			'notes',
			'closedCaptions',
			'singleNote',
			'sendFeedback',
			'displayQuickStartHost',
			'displayQuickStartAttendees',
			'supportE2E',
			'supportPKI',
			'HQvideo',
			'HDvideo',
			'viewVideoThumbs',
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
	 * @param boolean $audioVideo
	 */
	public function setAudioVideo($audioVideo)
	{
		$this->audioVideo = $audioVideo;
	}
	
	/**
	 * @return boolean $audioVideo
	 */
	public function getAudioVideo()
	{
		return $this->audioVideo;
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
	 * @param boolean $meetingRecord
	 */
	public function setMeetingRecord($meetingRecord)
	{
		$this->meetingRecord = $meetingRecord;
	}
	
	/**
	 * @return boolean $meetingRecord
	 */
	public function getMeetingRecord()
	{
		return $this->meetingRecord;
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
	 * @param boolean $rcAppShare
	 */
	public function setRcAppShare($rcAppShare)
	{
		$this->rcAppShare = $rcAppShare;
	}
	
	/**
	 * @return boolean $rcAppShare
	 */
	public function getRcAppShare()
	{
		return $this->rcAppShare;
	}
	
	/**
	 * @param boolean $rcDesktopShare
	 */
	public function setRcDesktopShare($rcDesktopShare)
	{
		$this->rcDesktopShare = $rcDesktopShare;
	}
	
	/**
	 * @return boolean $rcDesktopShare
	 */
	public function getRcDesktopShare()
	{
		return $this->rcDesktopShare;
	}
	
	/**
	 * @param boolean $rcWebTour
	 */
	public function setRcWebTour($rcWebTour)
	{
		$this->rcWebTour = $rcWebTour;
	}
	
	/**
	 * @return boolean $rcWebTour
	 */
	public function getRcWebTour()
	{
		return $this->rcWebTour;
	}
	
	/**
	 * @param boolean $javaClient
	 */
	public function setJavaClient($javaClient)
	{
		$this->javaClient = $javaClient;
	}
	
	/**
	 * @return boolean $javaClient
	 */
	public function getJavaClient()
	{
		return $this->javaClient;
	}
	
	/**
	 * @param boolean $nativeClient
	 */
	public function setNativeClient($nativeClient)
	{
		$this->nativeClient = $nativeClient;
	}
	
	/**
	 * @return boolean $nativeClient
	 */
	public function getNativeClient()
	{
		return $this->nativeClient;
	}
	
	/**
	 * @param boolean $attendeeRecordMeeting
	 */
	public function setAttendeeRecordMeeting($attendeeRecordMeeting)
	{
		$this->attendeeRecordMeeting = $attendeeRecordMeeting;
	}
	
	/**
	 * @return boolean $attendeeRecordMeeting
	 */
	public function getAttendeeRecordMeeting()
	{
		return $this->attendeeRecordMeeting;
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
	 * @param boolean $enableReg
	 */
	public function setEnableReg($enableReg)
	{
		$this->enableReg = $enableReg;
	}
	
	/**
	 * @return boolean $enableReg
	 */
	public function getEnableReg()
	{
		return $this->enableReg;
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
	 * @param boolean $supportFeedback
	 */
	public function setSupportFeedback($supportFeedback)
	{
		$this->supportFeedback = $supportFeedback;
	}
	
	/**
	 * @return boolean $supportFeedback
	 */
	public function getSupportFeedback()
	{
		return $this->supportFeedback;
	}
	
	/**
	 * @param boolean $supportBreakoutSessions
	 */
	public function setSupportBreakoutSessions($supportBreakoutSessions)
	{
		$this->supportBreakoutSessions = $supportBreakoutSessions;
	}
	
	/**
	 * @return boolean $supportBreakoutSessions
	 */
	public function getSupportBreakoutSessions()
	{
		return $this->supportBreakoutSessions;
	}
	
	/**
	 * @param boolean $supportPanelists
	 */
	public function setSupportPanelists($supportPanelists)
	{
		$this->supportPanelists = $supportPanelists;
	}
	
	/**
	 * @return boolean $supportPanelists
	 */
	public function getSupportPanelists()
	{
		return $this->supportPanelists;
	}
	
	/**
	 * @param boolean $supportRemoteComputer
	 */
	public function setSupportRemoteComputer($supportRemoteComputer)
	{
		$this->supportRemoteComputer = $supportRemoteComputer;
	}
	
	/**
	 * @return boolean $supportRemoteComputer
	 */
	public function getSupportRemoteComputer()
	{
		return $this->supportRemoteComputer;
	}
	
	/**
	 * @param boolean $supportShareWebContent
	 */
	public function setSupportShareWebContent($supportShareWebContent)
	{
		$this->supportShareWebContent = $supportShareWebContent;
	}
	
	/**
	 * @return boolean $supportShareWebContent
	 */
	public function getSupportShareWebContent()
	{
		return $this->supportShareWebContent;
	}
	
	/**
	 * @param boolean $supportUCFWebPages
	 */
	public function setSupportUCFWebPages($supportUCFWebPages)
	{
		$this->supportUCFWebPages = $supportUCFWebPages;
	}
	
	/**
	 * @return boolean $supportUCFWebPages
	 */
	public function getSupportUCFWebPages()
	{
		return $this->supportUCFWebPages;
	}
	
	/**
	 * @param boolean $supportUCFRichMedia
	 */
	public function setSupportUCFRichMedia($supportUCFRichMedia)
	{
		$this->supportUCFRichMedia = $supportUCFRichMedia;
	}
	
	/**
	 * @return boolean $supportUCFRichMedia
	 */
	public function getSupportUCFRichMedia()
	{
		return $this->supportUCFRichMedia;
	}
	
	/**
	 * @param boolean $autoDeleteAfterMeetingEnd
	 */
	public function setAutoDeleteAfterMeetingEnd($autoDeleteAfterMeetingEnd)
	{
		$this->autoDeleteAfterMeetingEnd = $autoDeleteAfterMeetingEnd;
	}
	
	/**
	 * @return boolean $autoDeleteAfterMeetingEnd
	 */
	public function getAutoDeleteAfterMeetingEnd()
	{
		return $this->autoDeleteAfterMeetingEnd;
	}
	
	/**
	 * @param boolean $viewAnyDoc
	 */
	public function setViewAnyDoc($viewAnyDoc)
	{
		$this->viewAnyDoc = $viewAnyDoc;
	}
	
	/**
	 * @return boolean $viewAnyDoc
	 */
	public function getViewAnyDoc()
	{
		return $this->viewAnyDoc;
	}
	
	/**
	 * @param boolean $viewAnyPage
	 */
	public function setViewAnyPage($viewAnyPage)
	{
		$this->viewAnyPage = $viewAnyPage;
	}
	
	/**
	 * @return boolean $viewAnyPage
	 */
	public function getViewAnyPage()
	{
		return $this->viewAnyPage;
	}
	
	/**
	 * @param boolean $allowContactPrivate
	 */
	public function setAllowContactPrivate($allowContactPrivate)
	{
		$this->allowContactPrivate = $allowContactPrivate;
	}
	
	/**
	 * @return boolean $allowContactPrivate
	 */
	public function getAllowContactPrivate()
	{
		return $this->allowContactPrivate;
	}
	
	/**
	 * @param boolean $chatHost
	 */
	public function setChatHost($chatHost)
	{
		$this->chatHost = $chatHost;
	}
	
	/**
	 * @return boolean $chatHost
	 */
	public function getChatHost()
	{
		return $this->chatHost;
	}
	
	/**
	 * @param boolean $chatPresenter
	 */
	public function setChatPresenter($chatPresenter)
	{
		$this->chatPresenter = $chatPresenter;
	}
	
	/**
	 * @return boolean $chatPresenter
	 */
	public function getChatPresenter()
	{
		return $this->chatPresenter;
	}
	
	/**
	 * @param boolean $chatAllAttendees
	 */
	public function setChatAllAttendees($chatAllAttendees)
	{
		$this->chatAllAttendees = $chatAllAttendees;
	}
	
	/**
	 * @return boolean $chatAllAttendees
	 */
	public function getChatAllAttendees()
	{
		return $this->chatAllAttendees;
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
	 * @param boolean $closedCaptions
	 */
	public function setClosedCaptions($closedCaptions)
	{
		$this->closedCaptions = $closedCaptions;
	}
	
	/**
	 * @return boolean $closedCaptions
	 */
	public function getClosedCaptions()
	{
		return $this->closedCaptions;
	}
	
	/**
	 * @param boolean $singleNote
	 */
	public function setSingleNote($singleNote)
	{
		$this->singleNote = $singleNote;
	}
	
	/**
	 * @return boolean $singleNote
	 */
	public function getSingleNote()
	{
		return $this->singleNote;
	}
	
	/**
	 * @param boolean $sendFeedback
	 */
	public function setSendFeedback($sendFeedback)
	{
		$this->sendFeedback = $sendFeedback;
	}
	
	/**
	 * @return boolean $sendFeedback
	 */
	public function getSendFeedback()
	{
		return $this->sendFeedback;
	}
	
	/**
	 * @param boolean $displayQuickStartHost
	 */
	public function setDisplayQuickStartHost($displayQuickStartHost)
	{
		$this->displayQuickStartHost = $displayQuickStartHost;
	}
	
	/**
	 * @return boolean $displayQuickStartHost
	 */
	public function getDisplayQuickStartHost()
	{
		return $this->displayQuickStartHost;
	}
	
	/**
	 * @param boolean $displayQuickStartAttendees
	 */
	public function setDisplayQuickStartAttendees($displayQuickStartAttendees)
	{
		$this->displayQuickStartAttendees = $displayQuickStartAttendees;
	}
	
	/**
	 * @return boolean $displayQuickStartAttendees
	 */
	public function getDisplayQuickStartAttendees()
	{
		return $this->displayQuickStartAttendees;
	}
	
	/**
	 * @param boolean $supportE2E
	 */
	public function setSupportE2E($supportE2E)
	{
		$this->supportE2E = $supportE2E;
	}
	
	/**
	 * @return boolean $supportE2E
	 */
	public function getSupportE2E()
	{
		return $this->supportE2E;
	}
	
	/**
	 * @param boolean $supportPKI
	 */
	public function setSupportPKI($supportPKI)
	{
		$this->supportPKI = $supportPKI;
	}
	
	/**
	 * @return boolean $supportPKI
	 */
	public function getSupportPKI()
	{
		return $this->supportPKI;
	}
	
	/**
	 * @param boolean $HQvideo
	 */
	public function setHQvideo($HQvideo)
	{
		$this->HQvideo = $HQvideo;
	}
	
	/**
	 * @return boolean $HQvideo
	 */
	public function getHQvideo()
	{
		return $this->HQvideo;
	}
	
	/**
	 * @param boolean $HDvideo
	 */
	public function setHDvideo($HDvideo)
	{
		$this->HDvideo = $HDvideo;
	}
	
	/**
	 * @return boolean $HDvideo
	 */
	public function getHDvideo()
	{
		return $this->HDvideo;
	}
	
	/**
	 * @param boolean $viewVideoThumbs
	 */
	public function setViewVideoThumbs($viewVideoThumbs)
	{
		$this->viewVideoThumbs = $viewVideoThumbs;
	}
	
	/**
	 * @return boolean $viewVideoThumbs
	 */
	public function getViewVideoThumbs()
	{
		return $this->viewVideoThumbs;
	}
	
}
		
