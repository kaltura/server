<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAuthenticationTypesCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::VALIDATE_AUTHENTICATION_TYPES);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$types = kConf::get('authenticationTypes', 'local', array());
		$requestParams = infraRequestUtils::parseRequestParams(false); // get only requewst params and url params

		KalturaLog::debug('Compares fields [ ' . print_r($types, true) . " ] to value [$requestParams]");

		foreach ($types as $authenticationType)
		{
			$keys = array_keys($requestParams);
			if( key_exists($authenticationType, $requestParams) || (int)preg_grep('/\d+:'.$authenticationType.'/',$keys))
			{
				KalturaLog::debug("Requset param $authenticationType exists, condition is true");
				return true;
			}
		}

		KalturaLog::debug("Authentication types doesn't exist on requests params. condition is false");
		return false;
	}

}
