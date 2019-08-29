<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kIpAddressCondition extends kMatchCondition
{
	const PARTNER_INTERNAL_IP = 'partnerInternalIp';
	const IP_ADDRESS_IN_RANGE = 'ipAddressInRange';

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

			$headerIp = infraRequestUtils::getIpFromHttpHeader($this->getHttpHeader(), $this->getAcceptInternalIps(), true);
			if ($headerIp)
			{
				$this->setExtraProperties(self::PARTNER_INTERNAL_IP, $headerIp);
			}
			return $headerIp;
		}
		
		kApiCache::addExtraField(kApiCache::ECF_IP, kApiCache::COND_IP_RANGE, $this->getStringValues($scope));
		return $scope->getIp();
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		$res = kIpAddressUtils::isIpInRanges($field, $value);
		if ($res)
		{
			$this->setExtraProperties(self::IP_ADDRESS_IN_RANGE, true);
		}
		return $res;
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
