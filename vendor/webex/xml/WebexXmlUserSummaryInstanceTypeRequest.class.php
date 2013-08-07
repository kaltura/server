<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlUserSummaryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlUseActiveType.class.php');
require_once(__DIR__ . '/WebexXmlUseWebACDUserRoleType.class.php');

class WebexXmlUserSummaryInstanceTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $webExId;
	
	/**
	 *
	 * @var string
	 */
	protected $firstName;
	
	/**
	 *
	 * @var string
	 */
	protected $lastName;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $registrationDate;
	
	/**
	 *
	 * @var WebexXmlUseActiveType
	 */
	protected $active;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var WebexXmlUseWebACDUserRoleType
	 */
	protected $webACD;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExId',
			'firstName',
			'lastName',
			'email',
			'registrationDate',
			'active',
			'timeZoneID',
			'webACD',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'webExId',
			'firstName',
			'lastName',
			'email',
			'active',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:userSummaryInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlUserSummaryInstanceType';
	}
	
	/**
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @param string $registrationDate
	 */
	public function setRegistrationDate($registrationDate)
	{
		$this->registrationDate = $registrationDate;
	}
	
	/**
	 * @param WebexXmlUseActiveType $active
	 */
	public function setActive(WebexXmlUseActiveType $active)
	{
		$this->active = $active;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @param WebexXmlUseWebACDUserRoleType $webACD
	 */
	public function setWebACD(WebexXmlUseWebACDUserRoleType $webACD)
	{
		$this->webACD = $webACD;
	}
	
}
		
