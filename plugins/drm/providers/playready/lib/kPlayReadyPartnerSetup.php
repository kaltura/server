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
			KalturaLog::debug("playready setup for partner ".$partnerId);
			list ($defaultPolicy, $rentalPolicy, $purchasePolicy, $subscriptionPolicy) = self::createPartnerPolicies($partnerId);
			self::createDefaultAccessControl($partnerId, $defaultPolicy, $rentalPolicy, $purchasePolicy, $subscriptionPolicy);
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
		KalturaLog::debug("Default policy id:".$defaultPolicy->getId());
							
		$rentalPolicy = 
		self::createPolicy(	$partnerId, 
							"rental_".$partnerId, 
							PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::RENTAL), 
							DrmLicenseExpirationPolicy::FIXED_DURATION, 
							7);
		KalturaLog::debug("Rental policy id:".$rentalPolicy->getId());
							
		$purchasePolicy = 
		self::createPolicy(	$partnerId, 
							"purchase_".$partnerId, 
							PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::PURCHASE), 
							DrmLicenseExpirationPolicy::UNLIMITED);	
		KalturaLog::debug("Purchase policy id:".$purchasePolicy->getId());
		
		$subscriptionPolicy = 
		self::createPolicy(	$partnerId, 
							"subscription_".$partnerId, 
							PlayReadyPlugin::getCoreValue('DrmLicenseScenario', PlayReadyLicenseScenario::SUBSCRIPTION), 
							DrmLicenseExpirationPolicy::FIXED_DURATION,
							7);	
		KalturaLog::debug("Subscription policy id:".$subscriptionPolicy->getId());
		
		
		return array($defaultPolicy, $rentalPolicy, $purchasePolicy, $subscriptionPolicy);
	}
	
	private static function createDefaultAccessControl($partnerId, $defaultPolicy, $rentalPolicy, $purchasePolicy, $subscriptionPolicy)
	{
		$accessControlProfile = new accessControl();
		$accessControlProfile->setDescription('Play Ready default access control');
		$accessControlProfile->setName('play_ready_default_'.$partnerId);
		$accessControlProfile->setPartnerId($partnerId);
		$accessControlProfile->setSystemName('play_ready_default_'.$partnerId);
		
		$rulePurchase = self::addAccessControlRule('scenario_purchase', $purchasePolicy->getId());		
		$ruleRental = self::addAccessControlRule('scenario_rental', $rentalPolicy->getId());		
		$ruleDefault = self::addAccessControlRule('scenario_default', $defaultPolicy->getId());
		$ruleSubscription = self::addAccessControlRule('scenario_subscription', $subscriptionPolicy->getId());
		
		$accessControlProfile->setRulesArray(array($rulePurchase, $ruleRental, $ruleDefault, $ruleSubscription));
		
		$accessControlProfile->save();
		
		KalturaLog::debug("Access control profile id:".$accessControlProfile->getId());
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
		$action = new kAccessControlPlayReadyPolicyAction();
		$action->setPolicyId($policyId);
		$rule->setConditions(array($condition));
		$rule->setActions(array($action));
		return $rule;
	}
}