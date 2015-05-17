<?php
/**
 * @package plugins.drm
 * @subpackage model.data
 */
class kAccessControlDrmPolicyAction extends kRuleAction
{
	/**
	 * @var int
	 */
	protected $policyId;
	
	public function __construct() 
	{
		parent::__construct(DrmAccessControlActionType::DRM_POLICY);
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
