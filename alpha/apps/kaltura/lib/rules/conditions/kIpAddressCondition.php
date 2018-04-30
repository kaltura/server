<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kIpAddressCondition extends kMatchCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::IP_ADDRESS);
		parent::__construct($not);
	}
	
	/**
	 * @var bool
	 */
	protected $acceptInternalIps;
	
	/**
	 * @var string
	 */
	protected $httpHeader;
	
	/**
	 * @param bool $acceptInternalIps
	 */
	public function setAcceptInternalIps($acceptInternalIps)
	{
	    $this->acceptInternalIps = $acceptInternalIps;
	}
	
	/**
	 * @return bool
	 */
	public function getAcceptInternalIps()
	{
	    return $this->acceptInternalIps;
	}
	
	/**
	 * @param string $httpHeader
	 */
	public function setHttpHeader($httpHeader)
	{
	    $this->httpHeader = $httpHeader;
	}
	
	/**
	 * @return string
	 */
	public function getHttpHeader()
	{
	    return $this->httpHeader;
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(kScope $scope)
	{
		if ($this->getHttpHeader() || $this->getAcceptInternalIps())
		{
			kApiCache::addExtraField(array("type" => kApiCache::ECF_IP,
					kApiCache::ECFD_IP_HTTP_HEADER => $this->getHttpHeader(),
					kApiCache::ECFD_IP_ACCEPT_INTERNAL_IPS => $this->getAcceptInternalIps()),
					kApiCache::COND_IP_RANGE, $this->getStringValues($scope));
		
			return infraRequestUtils::getIpFromHttpHeader($this->getHttpHeader(), $this->getAcceptInternalIps(), true);
		}
		
		kApiCache::addExtraField(kApiCache::ECF_IP, kApiCache::COND_IP_RANGE, $this->getStringValues($scope));
		return $scope->getIp();
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		return kIpAddressUtils::isIpInRanges($field, $value);
	}

	/**
	 * @param kScope $scope
	 * @return bool
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}
