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


	/* (non-PHPdoc)
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		kApiCache::addExtraField(kApiCache::ECF_USER_AGENT);

		if(!$scope)
			$scope = new kScope();

		return $scope->getUserAgent();
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage()
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('headerName');
	}

	/* (non-PHPdoc)
	 * @see kStringValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}

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
}