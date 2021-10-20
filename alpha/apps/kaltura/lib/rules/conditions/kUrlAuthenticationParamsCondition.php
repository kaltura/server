<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kUrlAuthenticationParamsCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::URL_AUTH_PARAMS);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$authParamNames = kConf::get('auth_data_param_names', 'local', array());
		$requestParams = infraRequestUtils::getUrlRequestParams();

		KalturaLog::debug('Compares fields [ ' . print_r($authParamNames, true) . ' ] to value [' . print_r($requestParams, true) .']');

		$filtereddKeys = array();
		foreach($requestParams as $key => $value)
		{
			$res = explode(':', $key);
			$filtereddKeys[] = strtolower(end($res));
		}

		$result = array_intersect($filtereddKeys, $authParamNames );
		if(!empty($result))
		{
			KalturaLog::debug("Request params condition is fullfilled with result " . print_r($result, true) . " - condition is true");
			return true;
		}

		KalturaLog::debug("Authentication types doesn't exist on requests params. condition is false");
		return false;
	}

}
