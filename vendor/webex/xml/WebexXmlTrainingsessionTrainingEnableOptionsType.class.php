<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionTrainingEnableOptionsType extends WebexXmlRequestType
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
	protected $trainingSessionRecord;
	
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
	protected $attendeeRecordTrainingSession;
	
	/**
	 *
	 * @var boolean
	 */
	protected $voip;
	
	/**
	 *
	 * @var boolean
	 */
	protected $faxIntoTrainingSession;
	
	/**
	 *
	 * @var boolean
	 */
	protected $autoDeleteAfterMeetingEnd;
	
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
	protected $supportUCFRichMedia;
	
	/**
	 *
	 * @var boolean
	 */
	protected $networkBasedRecord;
	
	/**
	 *
	 * @var boolean
	 */
	protected $presenterBreakoutSession;
	
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeBreakoutSession;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportPanelists;
	
	/**
	 *
	 * @var boolean
	 */
	protected $muteOnEntry;
	
	/**
	 *
	 * @var boolean
	 */
	protected $multiVideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $veryLargeSess;
	
	/**
	 *
	 * @var boolean
	 */
	protected $HQvideo;
	
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
	
			case 'trainingSessionRecord':
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
	
			case 'attendeeRecordTrainingSession':
				return 'boolean';
	
			case 'voip':
				return 'boolean';
	
			case 'faxIntoTrainingSession':
				return 'boolean';
	
			case 'autoDeleteAfterMeetingEnd':
				return 'boolean';
	
			case 'displayQuickStartHost':
				return 'boolean';
	
			case 'displayQuickStartAttendees':
				return 'boolean';
	
			case 'supportQandA':
				return 'boolean';
	
			case 'supportFeedback':
				return 'boolean';
	
			case 'supportBreakoutSessions':
				return 'boolean';
	
			case 'supportRemoteComputer':
				return 'boolean';
	
			case 'supportShareWebContent':
				return 'boolean';
	
			case 'supportUCFRichMedia':
				return 'boolean';
	
			case 'networkBasedRecord':
				return 'boolean';
	
			case 'presenterBreakoutSession':
				return 'boolean';
	
			case 'attendeeBreakoutSession':
				return 'boolean';
	
			case 'supportPanelists':
				return 'boolean';
	
			case 'muteOnEntry':
				return 'boolean';
	
			case 'multiVideo':
				return 'boolean';
	
			case 'veryLargeSess':
				return 'boolean';
	
			case 'HQvideo':
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
			'fileShare',
			'presentation',
			'applicationShare',
			'desktopShare',
			'webTour',
			'trainingSessionRecord',
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
			'attendeeRecordTrainingSession',
			'voip',
			'faxIntoTrainingSession',
			'autoDeleteAfterMeetingEnd',
			'displayQuickStartHost',
			'displayQuickStartAttendees',
			'supportQandA',
			'supportFeedback',
			'supportBreakoutSessions',
			'supportRemoteComputer',
			'supportShareWebContent',
			'supportUCFRichMedia',
			'networkBasedRecord',
			'presenterBreakoutSession',
			'attendeeBreakoutSession',
			'supportPanelists',
			'muteOnEntry',
			'multiVideo',
			'veryLargeSess',
			'HQvideo',
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
		return 'trainingEnableOptionsType';
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
	 * @param boolean $trainingSessionRecord
	 */
	public function setTrainingSessionRecord($trainingSessionRecord)
	{
		$this->trainingSessionRecord = $trainingSessionRecord;
	}
	
	/**
	 * @return boolean $trainingSessionRecord
	 */
	public function getTrainingSessionRecord()
	{
		return $this->trainingSessionRecord;
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
	 * @param boolean $attendeeRecordTrainingSession
	 */
	public function setAttendeeRecordTrainingSession($attendeeRecordTrainingSession)
	{
		$this->attendeeRecordTrainingSession = $attendeeRecordTrainingSession;
	}
	
	/**
	 * @return boolean $attendeeRecordTrainingSession
	 */
	public function getAttendeeRecordTrainingSession()
	{
		return $this->attendeeRecordTrainingSession;
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
	 * @param boolean $faxIntoTrainingSession
	 */
	public function setFaxIntoTrainingSession($faxIntoTrainingSession)
	{
		$this->faxIntoTrainingSession = $faxIntoTrainingSession;
	}
	
	/**
	 * @return boolean $faxIntoTrainingSession
	 */
	public function getFaxIntoTrainingSession()
	{
		return $this->faxIntoTrainingSession;
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
	 * @param boolean $networkBasedRecord
	 */
	public function setNetworkBasedRecord($networkBasedRecord)
	{
		$this->networkBasedRecord = $networkBasedRecord;
	}
	
	/**
	 * @return boolean $networkBasedRecord
	 */
	public function getNetworkBasedRecord()
	{
		return $this->networkBasedRecord;
	}
	
	/**
	 * @param boolean $presenterBreakoutSession
	 */
	public function setPresenterBreakoutSession($presenterBreakoutSession)
	{
		$this->presenterBreakoutSession = $presenterBreakoutSession;
	}
	
	/**
	 * @return boolean $presenterBreakoutSession
	 */
	public function getPresenterBreakoutSession()
	{
		return $this->presenterBreakoutSession;
	}
	
	/**
	 * @param boolean $attendeeBreakoutSession
	 */
	public function setAttendeeBreakoutSession($attendeeBreakoutSession)
	{
		$this->attendeeBreakoutSession = $attendeeBreakoutSession;
	}
	
	/**
	 * @return boolean $attendeeBreakoutSession
	 */
	public function getAttendeeBreakoutSession()
	{
		return $this->attendeeBreakoutSession;
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
	 * @param boolean $muteOnEntry
	 */
	public function setMuteOnEntry($muteOnEntry)
	{
		$this->muteOnEntry = $muteOnEntry;
	}
	
	/**
	 * @return boolean $muteOnEntry
	 */
	public function getMuteOnEntry()
	{
		return $this->muteOnEntry;
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
	 * @param boolean $veryLargeSess
	 */
	public function setVeryLargeSess($veryLargeSess)
	{
		$this->veryLargeSess = $veryLargeSess;
	}
	
	/**
	 * @return boolean $veryLargeSess
	 */
	public function getVeryLargeSess()
	{
		return $this->veryLargeSess;
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
	
}
		
