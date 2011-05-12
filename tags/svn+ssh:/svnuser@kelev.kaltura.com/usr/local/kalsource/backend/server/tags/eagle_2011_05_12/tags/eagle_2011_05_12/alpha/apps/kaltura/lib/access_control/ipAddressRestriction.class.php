<?php
/**
 * @package Core
 * @subpackage model.data
 */
class ipAddressRestriction extends baseRestriction
{	
	const IP_ADDRESS_LIST_DELIMETER = ',';
			
	/**
	 * Allow or restrict
	 * 
	 * @var int
	 */
	protected $type;
	
	/**
	 * List of ip address.
	 * @see kIpAddressUtils for supported address syntax types
	 * @var array
	 */
	private $ipAddressList;
	
	/**
	 * @param int $type IP_RESTRICTION_TYPE_RESTRICT_LIST / IP_RESTRICTION_TYPE_ALLOW_LIST
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return int IP_RESTRICTION_TYPE_RESTRICT_LIST / IP_RESTRICTION_TYPE_ALLOW_LIST
	 */
	public function getType()
	{
		return $this->type;	
	}
	
	/**
	 * Set ip list to the given $ipList
	 * @param string $ipList
	 */
	public function setIpAddressList($ipList)
	{
		// keep as an array so it won't be done later
		$this->ipAddressList = array_map('trim', explode(self::IP_ADDRESS_LIST_DELIMETER, $ipList));
	}
	
	/**
	 * Get ip list
	 * @return string
	 */
	public function getIpAddressList()
	{
		return implode(self::IP_ADDRESS_LIST_DELIMETER, $this->ipAddressList);
	}
		
	
	/* (non-PHPdoc)
	 * @see baseRestriction::isValid()
	 */
	public function isValid()
	{
		if ($this->type == accessControlListRestrictionType::ALLOW_LIST) {
			return $this->matchIpAddress();
		}
		else if ($this->type == accessControlListRestrictionType::RESTRICT_LIST) {
			return !$this->matchIpAddress();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Return true if the current request's ip matches the saved ips list
	 * @return bool
	 */
	protected function matchIpAddress()
	{
		$accessControlScope = $this->getAccessControlScope();
		$requestIp = $accessControlScope->getIp();	
		foreach ($this->ipAddressList as $checkIp)
		{
			if (kIpAddressUtils::isIpInRange($requestIp, $checkIp)) {
				return true;
			}
		}
		return false;
	}
	
	
	public function toString()
	{
		$strArray['type'] = $this->getType();
		$strArray['ipAddressList'] = $this->getIpAddressList();
		return serialize($strArray);	
	}
	
	public function populateFromString($str)
	{
		$strArray = unserialize($str);
		$this->setType($strArray['type']);
		$this->setIpAddressList($strArray['ipAddressList']);
	}

	
	
}
