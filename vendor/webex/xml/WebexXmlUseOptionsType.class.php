<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $firstNameVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $lastNameVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $addressVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $workPhoneVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $cellPhoneVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $pagerVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $faxVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $officeUrlVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $pictureVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $notifyOnNewMessage;
	
	/**
	 *
	 * @var boolean
	 */
	protected $notifyOnMeeting;
	
	/**
	 *
	 * @var boolean
	 */
	protected $followMeEnable;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emailVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $listInCategory;
	
	/**
	 *
	 * @var boolean
	 */
	protected $titleVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $folderRead;
	
	/**
	 *
	 * @var boolean
	 */
	protected $folderWrite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $messageVisible;
	
	/**
	 *
	 * @var boolean
	 */
	protected $iconSelect1;
	
	/**
	 *
	 * @var boolean
	 */
	protected $iconSelect2;
	
	/**
	 *
	 * @var boolean
	 */
	protected $acceptLinkRequest;
	
	/**
	 *
	 * @var boolean
	 */
	protected $holdOnLinkRequest;
	
	/**
	 *
	 * @var boolean
	 */
	protected $notifyOnLinkRequest;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportVideo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportApp;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportFileShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportDesktopShare;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportMeetingRecord;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportAppshareRemote;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportWebTourRemote;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportDesktopShareRemote;
	
	/**
	 *
	 * @var boolean
	 */
	protected $subscriptionOffice;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'firstNameVisible':
				return 'boolean';
	
			case 'lastNameVisible':
				return 'boolean';
	
			case 'addressVisible':
				return 'boolean';
	
			case 'workPhoneVisible':
				return 'boolean';
	
			case 'cellPhoneVisible':
				return 'boolean';
	
			case 'pagerVisible':
				return 'boolean';
	
			case 'faxVisible':
				return 'boolean';
	
			case 'officeUrlVisible':
				return 'boolean';
	
			case 'pictureVisible':
				return 'boolean';
	
			case 'notifyOnNewMessage':
				return 'boolean';
	
			case 'notifyOnMeeting':
				return 'boolean';
	
			case 'followMeEnable':
				return 'boolean';
	
			case 'emailVisible':
				return 'boolean';
	
			case 'listInCategory':
				return 'boolean';
	
			case 'titleVisible':
				return 'boolean';
	
			case 'folderRead':
				return 'boolean';
	
			case 'folderWrite':
				return 'boolean';
	
			case 'messageVisible':
				return 'boolean';
	
			case 'iconSelect1':
				return 'boolean';
	
			case 'iconSelect2':
				return 'boolean';
	
			case 'acceptLinkRequest':
				return 'boolean';
	
			case 'holdOnLinkRequest':
				return 'boolean';
	
			case 'notifyOnLinkRequest':
				return 'boolean';
	
			case 'supportVideo':
				return 'boolean';
	
			case 'supportApp':
				return 'boolean';
	
			case 'supportFileShare':
				return 'boolean';
	
			case 'supportDesktopShare':
				return 'boolean';
	
			case 'supportMeetingRecord':
				return 'boolean';
	
			case 'supportAppshareRemote':
				return 'boolean';
	
			case 'supportWebTourRemote':
				return 'boolean';
	
			case 'supportDesktopShareRemote':
				return 'boolean';
	
			case 'subscriptionOffice':
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
			'firstNameVisible',
			'lastNameVisible',
			'addressVisible',
			'workPhoneVisible',
			'cellPhoneVisible',
			'pagerVisible',
			'faxVisible',
			'officeUrlVisible',
			'pictureVisible',
			'notifyOnNewMessage',
			'notifyOnMeeting',
			'followMeEnable',
			'emailVisible',
			'listInCategory',
			'titleVisible',
			'folderRead',
			'folderWrite',
			'messageVisible',
			'iconSelect1',
			'iconSelect2',
			'acceptLinkRequest',
			'holdOnLinkRequest',
			'notifyOnLinkRequest',
			'supportVideo',
			'supportApp',
			'supportFileShare',
			'supportDesktopShare',
			'supportMeetingRecord',
			'supportAppshareRemote',
			'supportWebTourRemote',
			'supportDesktopShareRemote',
			'subscriptionOffice',
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
		return 'optionsType';
	}
	
	/**
	 * @param boolean $firstNameVisible
	 */
	public function setFirstNameVisible($firstNameVisible)
	{
		$this->firstNameVisible = $firstNameVisible;
	}
	
	/**
	 * @return boolean $firstNameVisible
	 */
	public function getFirstNameVisible()
	{
		return $this->firstNameVisible;
	}
	
	/**
	 * @param boolean $lastNameVisible
	 */
	public function setLastNameVisible($lastNameVisible)
	{
		$this->lastNameVisible = $lastNameVisible;
	}
	
	/**
	 * @return boolean $lastNameVisible
	 */
	public function getLastNameVisible()
	{
		return $this->lastNameVisible;
	}
	
	/**
	 * @param boolean $addressVisible
	 */
	public function setAddressVisible($addressVisible)
	{
		$this->addressVisible = $addressVisible;
	}
	
	/**
	 * @return boolean $addressVisible
	 */
	public function getAddressVisible()
	{
		return $this->addressVisible;
	}
	
	/**
	 * @param boolean $workPhoneVisible
	 */
	public function setWorkPhoneVisible($workPhoneVisible)
	{
		$this->workPhoneVisible = $workPhoneVisible;
	}
	
	/**
	 * @return boolean $workPhoneVisible
	 */
	public function getWorkPhoneVisible()
	{
		return $this->workPhoneVisible;
	}
	
	/**
	 * @param boolean $cellPhoneVisible
	 */
	public function setCellPhoneVisible($cellPhoneVisible)
	{
		$this->cellPhoneVisible = $cellPhoneVisible;
	}
	
	/**
	 * @return boolean $cellPhoneVisible
	 */
	public function getCellPhoneVisible()
	{
		return $this->cellPhoneVisible;
	}
	
	/**
	 * @param boolean $pagerVisible
	 */
	public function setPagerVisible($pagerVisible)
	{
		$this->pagerVisible = $pagerVisible;
	}
	
	/**
	 * @return boolean $pagerVisible
	 */
	public function getPagerVisible()
	{
		return $this->pagerVisible;
	}
	
	/**
	 * @param boolean $faxVisible
	 */
	public function setFaxVisible($faxVisible)
	{
		$this->faxVisible = $faxVisible;
	}
	
	/**
	 * @return boolean $faxVisible
	 */
	public function getFaxVisible()
	{
		return $this->faxVisible;
	}
	
	/**
	 * @param boolean $officeUrlVisible
	 */
	public function setOfficeUrlVisible($officeUrlVisible)
	{
		$this->officeUrlVisible = $officeUrlVisible;
	}
	
	/**
	 * @return boolean $officeUrlVisible
	 */
	public function getOfficeUrlVisible()
	{
		return $this->officeUrlVisible;
	}
	
	/**
	 * @param boolean $pictureVisible
	 */
	public function setPictureVisible($pictureVisible)
	{
		$this->pictureVisible = $pictureVisible;
	}
	
	/**
	 * @return boolean $pictureVisible
	 */
	public function getPictureVisible()
	{
		return $this->pictureVisible;
	}
	
	/**
	 * @param boolean $notifyOnNewMessage
	 */
	public function setNotifyOnNewMessage($notifyOnNewMessage)
	{
		$this->notifyOnNewMessage = $notifyOnNewMessage;
	}
	
	/**
	 * @return boolean $notifyOnNewMessage
	 */
	public function getNotifyOnNewMessage()
	{
		return $this->notifyOnNewMessage;
	}
	
	/**
	 * @param boolean $notifyOnMeeting
	 */
	public function setNotifyOnMeeting($notifyOnMeeting)
	{
		$this->notifyOnMeeting = $notifyOnMeeting;
	}
	
	/**
	 * @return boolean $notifyOnMeeting
	 */
	public function getNotifyOnMeeting()
	{
		return $this->notifyOnMeeting;
	}
	
	/**
	 * @param boolean $followMeEnable
	 */
	public function setFollowMeEnable($followMeEnable)
	{
		$this->followMeEnable = $followMeEnable;
	}
	
	/**
	 * @return boolean $followMeEnable
	 */
	public function getFollowMeEnable()
	{
		return $this->followMeEnable;
	}
	
	/**
	 * @param boolean $emailVisible
	 */
	public function setEmailVisible($emailVisible)
	{
		$this->emailVisible = $emailVisible;
	}
	
	/**
	 * @return boolean $emailVisible
	 */
	public function getEmailVisible()
	{
		return $this->emailVisible;
	}
	
	/**
	 * @param boolean $listInCategory
	 */
	public function setListInCategory($listInCategory)
	{
		$this->listInCategory = $listInCategory;
	}
	
	/**
	 * @return boolean $listInCategory
	 */
	public function getListInCategory()
	{
		return $this->listInCategory;
	}
	
	/**
	 * @param boolean $titleVisible
	 */
	public function setTitleVisible($titleVisible)
	{
		$this->titleVisible = $titleVisible;
	}
	
	/**
	 * @return boolean $titleVisible
	 */
	public function getTitleVisible()
	{
		return $this->titleVisible;
	}
	
	/**
	 * @param boolean $folderRead
	 */
	public function setFolderRead($folderRead)
	{
		$this->folderRead = $folderRead;
	}
	
	/**
	 * @return boolean $folderRead
	 */
	public function getFolderRead()
	{
		return $this->folderRead;
	}
	
	/**
	 * @param boolean $folderWrite
	 */
	public function setFolderWrite($folderWrite)
	{
		$this->folderWrite = $folderWrite;
	}
	
	/**
	 * @return boolean $folderWrite
	 */
	public function getFolderWrite()
	{
		return $this->folderWrite;
	}
	
	/**
	 * @param boolean $messageVisible
	 */
	public function setMessageVisible($messageVisible)
	{
		$this->messageVisible = $messageVisible;
	}
	
	/**
	 * @return boolean $messageVisible
	 */
	public function getMessageVisible()
	{
		return $this->messageVisible;
	}
	
	/**
	 * @param boolean $iconSelect1
	 */
	public function setIconSelect1($iconSelect1)
	{
		$this->iconSelect1 = $iconSelect1;
	}
	
	/**
	 * @return boolean $iconSelect1
	 */
	public function getIconSelect1()
	{
		return $this->iconSelect1;
	}
	
	/**
	 * @param boolean $iconSelect2
	 */
	public function setIconSelect2($iconSelect2)
	{
		$this->iconSelect2 = $iconSelect2;
	}
	
	/**
	 * @return boolean $iconSelect2
	 */
	public function getIconSelect2()
	{
		return $this->iconSelect2;
	}
	
	/**
	 * @param boolean $acceptLinkRequest
	 */
	public function setAcceptLinkRequest($acceptLinkRequest)
	{
		$this->acceptLinkRequest = $acceptLinkRequest;
	}
	
	/**
	 * @return boolean $acceptLinkRequest
	 */
	public function getAcceptLinkRequest()
	{
		return $this->acceptLinkRequest;
	}
	
	/**
	 * @param boolean $holdOnLinkRequest
	 */
	public function setHoldOnLinkRequest($holdOnLinkRequest)
	{
		$this->holdOnLinkRequest = $holdOnLinkRequest;
	}
	
	/**
	 * @return boolean $holdOnLinkRequest
	 */
	public function getHoldOnLinkRequest()
	{
		return $this->holdOnLinkRequest;
	}
	
	/**
	 * @param boolean $notifyOnLinkRequest
	 */
	public function setNotifyOnLinkRequest($notifyOnLinkRequest)
	{
		$this->notifyOnLinkRequest = $notifyOnLinkRequest;
	}
	
	/**
	 * @return boolean $notifyOnLinkRequest
	 */
	public function getNotifyOnLinkRequest()
	{
		return $this->notifyOnLinkRequest;
	}
	
	/**
	 * @param boolean $supportVideo
	 */
	public function setSupportVideo($supportVideo)
	{
		$this->supportVideo = $supportVideo;
	}
	
	/**
	 * @return boolean $supportVideo
	 */
	public function getSupportVideo()
	{
		return $this->supportVideo;
	}
	
	/**
	 * @param boolean $supportApp
	 */
	public function setSupportApp($supportApp)
	{
		$this->supportApp = $supportApp;
	}
	
	/**
	 * @return boolean $supportApp
	 */
	public function getSupportApp()
	{
		return $this->supportApp;
	}
	
	/**
	 * @param boolean $supportFileShare
	 */
	public function setSupportFileShare($supportFileShare)
	{
		$this->supportFileShare = $supportFileShare;
	}
	
	/**
	 * @return boolean $supportFileShare
	 */
	public function getSupportFileShare()
	{
		return $this->supportFileShare;
	}
	
	/**
	 * @param boolean $supportDesktopShare
	 */
	public function setSupportDesktopShare($supportDesktopShare)
	{
		$this->supportDesktopShare = $supportDesktopShare;
	}
	
	/**
	 * @return boolean $supportDesktopShare
	 */
	public function getSupportDesktopShare()
	{
		return $this->supportDesktopShare;
	}
	
	/**
	 * @param boolean $supportMeetingRecord
	 */
	public function setSupportMeetingRecord($supportMeetingRecord)
	{
		$this->supportMeetingRecord = $supportMeetingRecord;
	}
	
	/**
	 * @return boolean $supportMeetingRecord
	 */
	public function getSupportMeetingRecord()
	{
		return $this->supportMeetingRecord;
	}
	
	/**
	 * @param boolean $supportAppshareRemote
	 */
	public function setSupportAppshareRemote($supportAppshareRemote)
	{
		$this->supportAppshareRemote = $supportAppshareRemote;
	}
	
	/**
	 * @return boolean $supportAppshareRemote
	 */
	public function getSupportAppshareRemote()
	{
		return $this->supportAppshareRemote;
	}
	
	/**
	 * @param boolean $supportWebTourRemote
	 */
	public function setSupportWebTourRemote($supportWebTourRemote)
	{
		$this->supportWebTourRemote = $supportWebTourRemote;
	}
	
	/**
	 * @return boolean $supportWebTourRemote
	 */
	public function getSupportWebTourRemote()
	{
		return $this->supportWebTourRemote;
	}
	
	/**
	 * @param boolean $supportDesktopShareRemote
	 */
	public function setSupportDesktopShareRemote($supportDesktopShareRemote)
	{
		$this->supportDesktopShareRemote = $supportDesktopShareRemote;
	}
	
	/**
	 * @return boolean $supportDesktopShareRemote
	 */
	public function getSupportDesktopShareRemote()
	{
		return $this->supportDesktopShareRemote;
	}
	
	/**
	 * @param boolean $subscriptionOffice
	 */
	public function setSubscriptionOffice($subscriptionOffice)
	{
		$this->subscriptionOffice = $subscriptionOffice;
	}
	
	/**
	 * @return boolean $subscriptionOffice
	 */
	public function getSubscriptionOffice()
	{
		return $this->subscriptionOffice;
	}
	
}
		
