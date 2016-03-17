<?php

class kDrmPartnerSetup
{
	
	public static function setupPartner($partnerId)
	{
		$c = new Criteria();
		$c->add ( DrmPolicyPeer::PARTNER_ID, $partnerId );
		$c->add ( DrmPolicyPeer::STATUS, DrmPolicyStatus::ACTIVE);
		$c->add ( DrmPolicyPeer::PROVIDER, DrmProviderType::CENC);
		$policies = DrmPolicyPeer::doSelectOne($c);
		if(!count($policies))
		{
			KalturaLog::info("DRM setup for partner ".$partnerId);
			list ($defaultPolicy) = self::createPartnerPolicies($partnerId);
			self::createDefaultAccessControl($partnerId, $defaultPolicy);
		}
	}
	
	private static function createPartnerPolicies($partnerId)
	{
		$defaultPolicy = self::createPolicy(	$partnerId,
							"default_".$partnerId,
							"",
							1,
							1);
		KalturaLog::info("Default policy id:".$defaultPolicy->getId());

		return array($defaultPolicy);
	}
	
	private static function createDefaultAccessControl($partnerId, $defaultPolicy)//, $rentalPolicy, $purchasePolicy, $subscriptionPolicy)
	{
		$accessControlProfile = new accessControl();
		$accessControlProfile->setDescription('DRM default access control');
		$accessControlProfile->setName('drm_default_'.$partnerId);
		$accessControlProfile->setPartnerId($partnerId);
		$accessControlProfile->setSystemName('drm_default_'.$partnerId);

		$ruleDefault = self::addAccessControlRule($defaultPolicy->getId());

		$accessControlProfile->setRulesArray(array($ruleDefault));
		$accessControlProfile->save();
		KalturaLog::info("Access control profile id:".$accessControlProfile->getId());
	}

	private static function createPolicy($partnerId, $policyName, $scenario, $expirationPolicy, $duration = null)
	{
		$dbPolicy = new DrmPolicy();
		$dbPolicy->setName($policyName);
		$dbPolicy->setSystemName($policyName);
		if($duration)
			$dbPolicy->setDuration($duration);
		$dbPolicy->setLicenseExpirationPolicy($expirationPolicy);
		$dbPolicy->setLicenseType(PlayReadyPlugin::getCoreValue('DrmLicenseType', PlayReadyLicenseType::PERSISTENT));
		$dbPolicy->setPartnerId($partnerId);
		$dbPolicy->setProvider(DrmProviderType::CENC);
		$dbPolicy->setScenario($scenario);
		$dbPolicy->setStatus(DrmPolicyStatus::ACTIVE);
		$dbPolicy->save();

		return $dbPolicy;
	}

	private static function addAccessControlRule($policyId)
	{
		$rule = new kRule();
		$action = new kAccessControlDrmPolicyAction();
		$action->setPolicyId($policyId);
		$rule->setActions(array($action));
		return $rule;
	}
}