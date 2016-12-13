<?php

class kPlayReadyPartnerSetup
{
	
	public static function setupPartner($partnerId)
	{
		$c = new Criteria();
		$c->add ( DrmPolicyPeer::PARTNER_ID, $partnerId );
		$c->add ( DrmPolicyPeer::STATUS, DrmPolicyStatus::ACTIVE);
		$c->add ( DrmPolicyPeer::PROVIDER, PlayReadyPlugin::getPlayReadyProviderCoreValue());				
		$policies = DrmPolicyPeer::doSelectOne($c);
		if(!count($policies))
		{
			KalturaLog::info("playready setup for partner ".$partnerId);
			list ($defaultPolicy) = self::createPartnerPolicies($partnerId);
			self::createDefaultAccessControl($partnerId, $defaultPolicy);
		}
	}
	
	private static function createPartnerPolicies($partnerId)
	{
		$defaultPolicy = 
		self::createPolicy(	$partnerId, 
							"default_".$partnerId, 
							PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::PROTECTION), 
							DrmLicenseExpirationPolicy::FIXED_DURATION, 
							1);
		KalturaLog::info("Default policy id:".$defaultPolicy->getId());
		return array($defaultPolicy);
	}
	
	private static function createDefaultAccessControl($partnerId, $defaultPolicy)
	{
		$accessControlProfile = new accessControl();
		$accessControlProfile->setDescription('Play Ready default access control');
		$accessControlProfile->setName('play_ready_default_'.$partnerId);
		$accessControlProfile->setPartnerId($partnerId);
		$accessControlProfile->setSystemName('play_ready_default_'.$partnerId);
		
		$ruleDefault = self::addAccessControlRule('scenario_default', $defaultPolicy->getId());

		$accessControlProfile->setRulesArray(array($ruleDefault));
		
		$accessControlProfile->save();
		
		KalturaLog::info("Access control profile id:".$accessControlProfile->getId());
	}
	
	private static function createPolicy($partnerId, $policyName, $scenario, $expirationPolicy, $duration = null)
	{
		$dbPolicy = new PlayReadyPolicy();
		
		$dbPolicy->setName($policyName);
		$dbPolicy->setSystemName($policyName);
		if($duration)
			$dbPolicy->setDuration($duration);
		$dbPolicy->setLicenseExpirationPolicy($expirationPolicy);
		$dbPolicy->setLicenseType(PlayReadyPlugin::getCoreValue('DrmLicenseType', PlayReadyLicenseType::PERSISTENT));
		$dbPolicy->setPartnerId($partnerId);
		$dbPolicy->setProvider(PlayReadyPlugin::getPlayReadyProviderCoreValue());
		$dbPolicy->setScenario($scenario);
		if($scenario == PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::RENTAL) || 
			$scenario == PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::SUBSCRIPTION) )
		{
			$playRight = new PlayReadyPlayRight();
			$playRight->setFirstPlayExpiration(48);
			$dbPolicy->setRights(array($playRight));
		}
		if($scenario == PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::PURCHASE))
		{
			$copyRight = new PlayReadyCopyRight();
			$copyRight->setCopyEnablers(array(PlayReadyCopyEnablerType::DEVICE, PlayReadyCopyEnablerType::PC));
			$copyRight->setCopyCount(100);			
			$dbPolicy->setRights(array($copyRight));
		}
		$dbPolicy->setStatus(DrmPolicyStatus::ACTIVE);
				
		$dbPolicy->save();
		
		return $dbPolicy;
	}
	
	private static function addAccessControlRule($priviledge, $policyId)
	{
		$rule = new kRule();
		$condition = new kAuthenticatedCondition();
		$condition->setPrivileges(array($priviledge));
		$action = new kAccessControlDrmPolicyAction();
		$action->setPolicyId($policyId);
		$rule->setConditions(array($condition));
		$rule->setActions(array($action));
		$rule->setContexts(array(ContextType::PLAY));
		return $rule;
	}
}