<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlAddRequestHostPrefixAction extends kRuleAction 
{
	/**
	 * @var string
	 */
	protected $requestHostPrefix;
	
	
	public function __construct() 
	{
		parent::__construct(RuleActionType::ADD_REQUEST_HOST_PREFIX);
	}
	
	/**
	 * @return string
	 */
	public function getRequestHostPrefix() 
	{
		return $this->requestHostPrefix;
	}
	/**
	 * @param string $requestHostPrefix
	 */
	public function setRequestHostPrefix($requestHostPrefix) 
	{
		$this->requestHostPrefix = $requestHostPrefix;
	}
}
