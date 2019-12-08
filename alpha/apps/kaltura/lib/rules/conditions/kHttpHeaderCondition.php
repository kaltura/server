<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kHttpHeaderCondition extends kRegexCondition
{
	/**
	 * @var string
	 */
	protected $headerName;


	/**
	 * @return string
	 */
	public function getHeaderName()
	{
		return $this->headerName;
	}

	/**
	 * @param string $headerName
	 */
	public function setHeaderName($headerName)
	{
		$this->headerName = $headerName;
	}

	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::HTTP_HEADER);
		parent::__construct($not);
	}

	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(kScope $scope)
	{
		kApiCache::addExtraField(array(kApiCache::ECFD_IP_HTTP_HEADER => $this->getHeaderName), kApiCache::COND_REGEX, $this->getStringValues($scope));
		$headerValue = isset($_SERVER[$this->getHeaderName()]) ? $_SERVER[$this->getHeaderName()] : null;
		return $headerValue;
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}