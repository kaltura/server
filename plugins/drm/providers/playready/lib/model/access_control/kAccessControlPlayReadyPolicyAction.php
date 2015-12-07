<?php
/**
 * @package plugins.playReady
 * @subpackage model.data
 */
class kAccessControlPlayReadyPolicyAction extends kRuleAction 
{
	/**
	 * @var int
	 */
	protected $policyId;
	
	public function __construct() 
	{
		parent::__construct(PlayReadyAccessControlActionType::DRM_POLICY);
	}
	
	/**
	 * @return the $policyId
	 */
	public function getPolicyId() 
	{
		return $this->policyId;
	}

	/**
	 * @param int $policyId
	 */
	public function setPolicyId($policyId) 
	{
		$this->policyId = $policyId;
	}
}
