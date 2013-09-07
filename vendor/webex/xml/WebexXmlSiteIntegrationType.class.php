<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteIntegrationType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $outlook;
	
	/**
	 *
	 * @var boolean
	 */
	protected $lotusNotes;
	
	/**
	 *
	 * @var boolean
	 */
	protected $oneClick;
	
	/**
	 *
	 * @var boolean
	 */
	protected $showSysTrayIcon;
	
	/**
	 *
	 * @var boolean
	 */
	protected $office;
	
	/**
	 *
	 * @var boolean
	 */
	protected $excel;
	
	/**
	 *
	 * @var boolean
	 */
	protected $powerPoint;
	
	/**
	 *
	 * @var boolean
	 */
	protected $word;
	
	/**
	 *
	 * @var boolean
	 */
	protected $IE;
	
	/**
	 *
	 * @var boolean
	 */
	protected $firefox;
	
	/**
	 *
	 * @var boolean
	 */
	protected $explorerRightClick;
	
	/**
	 *
	 * @var boolean
	 */
	protected $instantMessenger;
	
	/**
	 *
	 * @var boolean
	 */
	protected $aolMessenger;
	
	/**
	 *
	 * @var boolean
	 */
	protected $googleTalk;
	
	/**
	 *
	 * @var boolean
	 */
	protected $lotusSametime;
	
	/**
	 *
	 * @var boolean
	 */
	protected $skype;
	
	/**
	 *
	 * @var boolean
	 */
	protected $windowsMessenger;
	
	/**
	 *
	 * @var boolean
	 */
	protected $yahooMessenger;
	
	/**
	 *
	 * @var boolean
	 */
	protected $ciscoIPPhone;
	
	/**
	 *
	 * @var string
	 */
	protected $ciscoCUAEURL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $pcNow;
	
	/**
	 *
	 * @var boolean
	 */
	protected $iGoogle;
	
	/**
	 *
	 * @var boolean
	 */
	protected $iPhoneDusting;
	
	/**
	 *
	 * @var string
	 */
	protected $CUMAURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'outlook':
				return 'boolean';
	
			case 'lotusNotes':
				return 'boolean';
	
			case 'oneClick':
				return 'boolean';
	
			case 'showSysTrayIcon':
				return 'boolean';
	
			case 'office':
				return 'boolean';
	
			case 'excel':
				return 'boolean';
	
			case 'powerPoint':
				return 'boolean';
	
			case 'word':
				return 'boolean';
	
			case 'IE':
				return 'boolean';
	
			case 'firefox':
				return 'boolean';
	
			case 'explorerRightClick':
				return 'boolean';
	
			case 'instantMessenger':
				return 'boolean';
	
			case 'aolMessenger':
				return 'boolean';
	
			case 'googleTalk':
				return 'boolean';
	
			case 'lotusSametime':
				return 'boolean';
	
			case 'skype':
				return 'boolean';
	
			case 'windowsMessenger':
				return 'boolean';
	
			case 'yahooMessenger':
				return 'boolean';
	
			case 'ciscoIPPhone':
				return 'boolean';
	
			case 'ciscoCUAEURL':
				return 'string';
	
			case 'pcNow':
				return 'boolean';
	
			case 'iGoogle':
				return 'boolean';
	
			case 'iPhoneDusting':
				return 'boolean';
	
			case 'CUMAURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'outlook',
			'lotusNotes',
			'oneClick',
			'showSysTrayIcon',
			'office',
			'excel',
			'powerPoint',
			'word',
			'IE',
			'firefox',
			'explorerRightClick',
			'instantMessenger',
			'aolMessenger',
			'googleTalk',
			'lotusSametime',
			'skype',
			'windowsMessenger',
			'yahooMessenger',
			'ciscoIPPhone',
			'ciscoCUAEURL',
			'pcNow',
			'iGoogle',
			'iPhoneDusting',
			'CUMAURL',
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
		return 'integrationType';
	}
	
	/**
	 * @param boolean $outlook
	 */
	public function setOutlook($outlook)
	{
		$this->outlook = $outlook;
	}
	
	/**
	 * @return boolean $outlook
	 */
	public function getOutlook()
	{
		return $this->outlook;
	}
	
	/**
	 * @param boolean $lotusNotes
	 */
	public function setLotusNotes($lotusNotes)
	{
		$this->lotusNotes = $lotusNotes;
	}
	
	/**
	 * @return boolean $lotusNotes
	 */
	public function getLotusNotes()
	{
		return $this->lotusNotes;
	}
	
	/**
	 * @param boolean $oneClick
	 */
	public function setOneClick($oneClick)
	{
		$this->oneClick = $oneClick;
	}
	
	/**
	 * @return boolean $oneClick
	 */
	public function getOneClick()
	{
		return $this->oneClick;
	}
	
	/**
	 * @param boolean $showSysTrayIcon
	 */
	public function setShowSysTrayIcon($showSysTrayIcon)
	{
		$this->showSysTrayIcon = $showSysTrayIcon;
	}
	
	/**
	 * @return boolean $showSysTrayIcon
	 */
	public function getShowSysTrayIcon()
	{
		return $this->showSysTrayIcon;
	}
	
	/**
	 * @param boolean $office
	 */
	public function setOffice($office)
	{
		$this->office = $office;
	}
	
	/**
	 * @return boolean $office
	 */
	public function getOffice()
	{
		return $this->office;
	}
	
	/**
	 * @param boolean $excel
	 */
	public function setExcel($excel)
	{
		$this->excel = $excel;
	}
	
	/**
	 * @return boolean $excel
	 */
	public function getExcel()
	{
		return $this->excel;
	}
	
	/**
	 * @param boolean $powerPoint
	 */
	public function setPowerPoint($powerPoint)
	{
		$this->powerPoint = $powerPoint;
	}
	
	/**
	 * @return boolean $powerPoint
	 */
	public function getPowerPoint()
	{
		return $this->powerPoint;
	}
	
	/**
	 * @param boolean $word
	 */
	public function setWord($word)
	{
		$this->word = $word;
	}
	
	/**
	 * @return boolean $word
	 */
	public function getWord()
	{
		return $this->word;
	}
	
	/**
	 * @param boolean $IE
	 */
	public function setIE($IE)
	{
		$this->IE = $IE;
	}
	
	/**
	 * @return boolean $IE
	 */
	public function getIE()
	{
		return $this->IE;
	}
	
	/**
	 * @param boolean $firefox
	 */
	public function setFirefox($firefox)
	{
		$this->firefox = $firefox;
	}
	
	/**
	 * @return boolean $firefox
	 */
	public function getFirefox()
	{
		return $this->firefox;
	}
	
	/**
	 * @param boolean $explorerRightClick
	 */
	public function setExplorerRightClick($explorerRightClick)
	{
		$this->explorerRightClick = $explorerRightClick;
	}
	
	/**
	 * @return boolean $explorerRightClick
	 */
	public function getExplorerRightClick()
	{
		return $this->explorerRightClick;
	}
	
	/**
	 * @param boolean $instantMessenger
	 */
	public function setInstantMessenger($instantMessenger)
	{
		$this->instantMessenger = $instantMessenger;
	}
	
	/**
	 * @return boolean $instantMessenger
	 */
	public function getInstantMessenger()
	{
		return $this->instantMessenger;
	}
	
	/**
	 * @param boolean $aolMessenger
	 */
	public function setAolMessenger($aolMessenger)
	{
		$this->aolMessenger = $aolMessenger;
	}
	
	/**
	 * @return boolean $aolMessenger
	 */
	public function getAolMessenger()
	{
		return $this->aolMessenger;
	}
	
	/**
	 * @param boolean $googleTalk
	 */
	public function setGoogleTalk($googleTalk)
	{
		$this->googleTalk = $googleTalk;
	}
	
	/**
	 * @return boolean $googleTalk
	 */
	public function getGoogleTalk()
	{
		return $this->googleTalk;
	}
	
	/**
	 * @param boolean $lotusSametime
	 */
	public function setLotusSametime($lotusSametime)
	{
		$this->lotusSametime = $lotusSametime;
	}
	
	/**
	 * @return boolean $lotusSametime
	 */
	public function getLotusSametime()
	{
		return $this->lotusSametime;
	}
	
	/**
	 * @param boolean $skype
	 */
	public function setSkype($skype)
	{
		$this->skype = $skype;
	}
	
	/**
	 * @return boolean $skype
	 */
	public function getSkype()
	{
		return $this->skype;
	}
	
	/**
	 * @param boolean $windowsMessenger
	 */
	public function setWindowsMessenger($windowsMessenger)
	{
		$this->windowsMessenger = $windowsMessenger;
	}
	
	/**
	 * @return boolean $windowsMessenger
	 */
	public function getWindowsMessenger()
	{
		return $this->windowsMessenger;
	}
	
	/**
	 * @param boolean $yahooMessenger
	 */
	public function setYahooMessenger($yahooMessenger)
	{
		$this->yahooMessenger = $yahooMessenger;
	}
	
	/**
	 * @return boolean $yahooMessenger
	 */
	public function getYahooMessenger()
	{
		return $this->yahooMessenger;
	}
	
	/**
	 * @param boolean $ciscoIPPhone
	 */
	public function setCiscoIPPhone($ciscoIPPhone)
	{
		$this->ciscoIPPhone = $ciscoIPPhone;
	}
	
	/**
	 * @return boolean $ciscoIPPhone
	 */
	public function getCiscoIPPhone()
	{
		return $this->ciscoIPPhone;
	}
	
	/**
	 * @param string $ciscoCUAEURL
	 */
	public function setCiscoCUAEURL($ciscoCUAEURL)
	{
		$this->ciscoCUAEURL = $ciscoCUAEURL;
	}
	
	/**
	 * @return string $ciscoCUAEURL
	 */
	public function getCiscoCUAEURL()
	{
		return $this->ciscoCUAEURL;
	}
	
	/**
	 * @param boolean $pcNow
	 */
	public function setPcNow($pcNow)
	{
		$this->pcNow = $pcNow;
	}
	
	/**
	 * @return boolean $pcNow
	 */
	public function getPcNow()
	{
		return $this->pcNow;
	}
	
	/**
	 * @param boolean $iGoogle
	 */
	public function setIGoogle($iGoogle)
	{
		$this->iGoogle = $iGoogle;
	}
	
	/**
	 * @return boolean $iGoogle
	 */
	public function getIGoogle()
	{
		return $this->iGoogle;
	}
	
	/**
	 * @param boolean $iPhoneDusting
	 */
	public function setIPhoneDusting($iPhoneDusting)
	{
		$this->iPhoneDusting = $iPhoneDusting;
	}
	
	/**
	 * @return boolean $iPhoneDusting
	 */
	public function getIPhoneDusting()
	{
		return $this->iPhoneDusting;
	}
	
	/**
	 * @param string $CUMAURL
	 */
	public function setCUMAURL($CUMAURL)
	{
		$this->CUMAURL = $CUMAURL;
	}
	
	/**
	 * @return string $CUMAURL
	 */
	public function getCUMAURL()
	{
		return $this->CUMAURL;
	}
	
}
		
