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
		$this->setType(ConditionType::URL_AUTH_PARAMS);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$authParamNames = kConf::get('auth_data_param_names', 'local', array());
		$params = infraRequestUtils::getUrlRequestParams();
		$requestParams = array_replace_recursive($_POST, $_FILES, $_GET, $params);

		KalturaLog::debug('Compares fields [ ' . print_r($authParamNames, true) . ' ] to value [' . print_r($requestParams, true) .']');

		$filteredKeys = array();
		foreach($requestParams as $key => $value)
		{
			$res = preg_split('/:/', $key);
			$filteredKeys[] = end($res);
		}

		$result = array_intersect($filteredKeys,$authParamNames );
		if(!empty($result))
		{
			KalturaLog::debug("Request params condition is fullfilled, condition is true");
			return true;
		}

		KalturaLog::debug("Authentication types doesn't exist on requests params. condition is false");
		return false;
	}

}
