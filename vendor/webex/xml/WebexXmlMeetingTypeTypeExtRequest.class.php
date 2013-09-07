<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlMeetingTypeTypeExt.class.php');
require_once(__DIR__ . '/WebexXmlMtgtypeActiveType.class.php');
require_once(__DIR__ . '/WebexXmlMtgtypeLimitsType.class.php');
require_once(__DIR__ . '/WebexXmlMtgtypeOptionsType.class.php');
require_once(__DIR__ . '/WebexXmlMtgtypePhoneNumbersType.class.php');

class WebexXmlMeetingTypeTypeExtRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $productCodePrefix;
	
	/**
	 *
	 * @var WebexXmlMtgtypeActiveType
	 */
	protected $active;
	
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var string
	 */
	protected $displayName;
	
	/**
	 *
	 * @var WebexXmlMtgtypeLimitsType
	 */
	protected $limits;
	
	/**
	 *
	 * @var WebexXmlMtgtypeOptionsType
	 */
	protected $options;
	
	/**
	 *
	 * @var WebexXmlMtgtypePhoneNumbersType
	 */
	protected $phoneNumbers;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'productCodePrefix',
			'active',
			'name',
			'displayName',
			'limits',
			'options',
			'phoneNumbers',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'productCodePrefix',
			'active',
			'name',
			'options',
			'phoneNumbers',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'meetingtype';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'meetingtype:meetingTypeTypeExt';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlMeetingTypeTypeExt';
	}
	
	/**
	 * @param string $productCodePrefix
	 */
	public function setProductCodePrefix($productCodePrefix)
	{
		$this->productCodePrefix = $productCodePrefix;
	}
	
	/**
	 * @param WebexXmlMtgtypeActiveType $active
	 */
	public function setActive(WebexXmlMtgtypeActiveType $active)
	{
		$this->active = $active;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @param string $displayName
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}
	
	/**
	 * @param WebexXmlMtgtypeLimitsType $limits
	 */
	public function setLimits(WebexXmlMtgtypeLimitsType $limits)
	{
		$this->limits = $limits;
	}
	
	/**
	 * @param WebexXmlMtgtypeOptionsType $options
	 */
	public function setOptions(WebexXmlMtgtypeOptionsType $options)
	{
		$this->options = $options;
	}
	
	/**
	 * @param WebexXmlMtgtypePhoneNumbersType $phoneNumbers
	 */
	public function setPhoneNumbers(WebexXmlMtgtypePhoneNumbersType $phoneNumbers)
	{
		$this->phoneNumbers = $phoneNumbers;
	}
	
}
		
