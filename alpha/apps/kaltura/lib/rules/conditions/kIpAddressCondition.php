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
	const PARTNER_INTERNAL = 'partnerInternal';

	const PARTNER_INTERNAL_IP = 'partnerInternalIp';

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
		$res = kIpAddressUtils::isIpInRanges($field, $value);
		//The assumption is that if we have a HTTP header set and that the IP is in range it comes from an internal IP source.
		if ($res && $this->getHttpHeader())
		{
			$this->setExtraProperties(self::PARTNER_INTERNAL, true);
			$this->setExtraProperties(self::PARTNER_INTERNAL_IP, $field);
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
