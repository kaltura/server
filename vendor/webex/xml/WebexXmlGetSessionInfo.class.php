<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/string.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXml.class.php');
require_once(__DIR__ . '/WebexXmlEpAttendeeRoleType.class.php');

class WebexXmlGetSessionInfo extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $presenter;
	
	/**
	 *
	 * @var string
	 */
	protected $panelistsInfo;
	
	/**
	 *
	 * @var string
	 */
	protected $programName;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionkey;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var string
	 */
	protected $verifyFlashMediaURL;
	
	/**
	 *
	 * @var string
	 */
	protected $verifyWinMediaURL;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $accessControl;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $metaData;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $telephony;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $material;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hasInSessionTest;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hasWebsiteTest;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $test;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $host;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $schedule;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $attendeeOptions;
	
	/**
	 *
	 * @var string
	 */
	protected $audioStatus;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isAudioOnly;
	
	/**
	 *
	 * @var boolean
	 */
	protected $telePresence;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isAlternateHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isCreator;
	
	/**
	 *
	 * @var string
	 */
	protected $hostKey;
	
	/**
	 *
	 * @var boolean
	 */
	protected $supportE2E;
	
	/**
	 *
	 * @var WebexXmlEpAttendeeRoleType
	 */
	protected $attendeeRole;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'status':
				return 'string';
	
			case 'presenter':
				return 'WebexXmlArray<string>';
	
			case 'panelistsInfo':
				return 'string';
	
			case 'programName':
				return 'string';
	
			case 'sessionkey':
				return 'long';
	
			case 'confID':
				return 'long';
	
			case 'verifyFlashMediaURL':
				return 'string';
	
			case 'verifyWinMediaURL':
				return 'string';
	
			case 'accessControl':
				return 'WebexXml';
	
			case 'metaData':
				return 'WebexXml';
	
			case 'telephony':
				return 'WebexXml';
	
			case 'material':
				return 'WebexXml';
	
			case 'hasInSessionTest':
				return 'boolean';
	
			case 'hasWebsiteTest':
				return 'boolean';
	
			case 'test':
				return 'WebexXmlArray<WebexXml>';
	
			case 'host':
				return 'WebexXml';
	
			case 'schedule':
				return 'WebexXml';
	
			case 'attendeeOptions':
				return 'WebexXml';
	
			case 'audioStatus':
				return 'string';
	
			case 'isAudioOnly':
				return 'boolean';
	
			case 'telePresence':
				return 'boolean';
	
			case 'isAlternateHost':
				return 'boolean';
	
			case 'isCreator':
				return 'boolean';
	
			case 'hostKey':
				return 'string';
	
			case 'supportE2E':
				return 'boolean';
	
			case 'attendeeRole':
				return 'WebexXmlEpAttendeeRoleType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @return WebexXmlArray $presenter
	 */
	public function getPresenter()
	{
		return $this->presenter;
	}
	
	/**
	 * @return string $panelistsInfo
	 */
	public function getPanelistsInfo()
	{
		return $this->panelistsInfo;
	}
	
	/**
	 * @return string $programName
	 */
	public function getProgramName()
	{
		return $this->programName;
	}
	
	/**
	 * @return long $sessionkey
	 */
	public function getSessionkey()
	{
		return $this->sessionkey;
	}
	
	/**
	 * @return long $confID
	 */
	public function getConfID()
	{
		return $this->confID;
	}
	
	/**
	 * @return string $verifyFlashMediaURL
	 */
	public function getVerifyFlashMediaURL()
	{
		return $this->verifyFlashMediaURL;
	}
	
	/**
	 * @return string $verifyWinMediaURL
	 */
	public function getVerifyWinMediaURL()
	{
		return $this->verifyWinMediaURL;
	}
	
	/**
	 * @return WebexXml $accessControl
	 */
	public function getAccessControl()
	{
		return $this->accessControl;
	}
	
	/**
	 * @return WebexXml $metaData
	 */
	public function getMetaData()
	{
		return $this->metaData;
	}
	
	/**
	 * @return WebexXml $telephony
	 */
	public function getTelephony()
	{
		return $this->telephony;
	}
	
	/**
	 * @return WebexXml $material
	 */
	public function getMaterial()
	{
		return $this->material;
	}
	
	/**
	 * @return boolean $hasInSessionTest
	 */
	public function getHasInSessionTest()
	{
		return $this->hasInSessionTest;
	}
	
	/**
	 * @return boolean $hasWebsiteTest
	 */
	public function getHasWebsiteTest()
	{
		return $this->hasWebsiteTest;
	}
	
	/**
	 * @return WebexXmlArray $test
	 */
	public function getTest()
	{
		return $this->test;
	}
	
	/**
	 * @return WebexXml $host
	 */
	public function getHost()
	{
		return $this->host;
	}
	
	/**
	 * @return WebexXml $schedule
	 */
	public function getSchedule()
	{
		return $this->schedule;
	}
	
	/**
	 * @return WebexXml $attendeeOptions
	 */
	public function getAttendeeOptions()
	{
		return $this->attendeeOptions;
	}
	
	/**
	 * @return string $audioStatus
	 */
	public function getAudioStatus()
	{
		return $this->audioStatus;
	}
	
	/**
	 * @return boolean $isAudioOnly
	 */
	public function getIsAudioOnly()
	{
		return $this->isAudioOnly;
	}
	
	/**
	 * @return boolean $telePresence
	 */
	public function getTelePresence()
	{
		return $this->telePresence;
	}
	
	/**
	 * @return boolean $isAlternateHost
	 */
	public function getIsAlternateHost()
	{
		return $this->isAlternateHost;
	}
	
	/**
	 * @return boolean $isCreator
	 */
	public function getIsCreator()
	{
		return $this->isCreator;
	}
	
	/**
	 * @return string $hostKey
	 */
	public function getHostKey()
	{
		return $this->hostKey;
	}
	
	/**
	 * @return boolean $supportE2E
	 */
	public function getSupportE2E()
	{
		return $this->supportE2E;
	}
	
	/**
	 * @return WebexXmlEpAttendeeRoleType $attendeeRole
	 */
	public function getAttendeeRole()
	{
		return $this->attendeeRole;
	}
	
}

