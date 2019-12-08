<?php
/**
 * Returns the current request http headers
 * @package Core
 * @subpackage model.data
 */

class kHttpHeaderContextField extends kStringField
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
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		kApiCache::addExtraField(kApiCache::ECFD_IP_HTTP_HEADER);

		if(!$scope)
			$scope = new kScope();

		$headerValue = isset($_SERVER[$this->getHeaderName()]) ? $_SERVER[$this->getHeaderName()] : null;
		return $headerValue;
	}

	/* (non-PHPdoc)
	 * @see kStringValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}

}