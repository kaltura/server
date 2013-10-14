<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kSiteCondition extends kMatchCondition
{
	/**
	 * Indicates that global whitelist domains already appended 
	 * @var bool
	 */
	private $globalWhitelistDomainsAppended = false;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::SITE);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(kScope $scope)
	{
		$referrer = $scope->getReferrer();
		return requestUtils::parseUrlHost($referrer);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$referrer = $scope->getReferrer();

		if ($this->getNot()===true && !$this->globalWhitelistDomainsAppended && strpos($referrer, "kwidget") === false && kConf::hasParam("global_whitelisted_domains"))
		{
			$ks = $scope->getKs();
			if (!$ks || !in_array($ks->partner_id, kConf::get('global_whitelisted_domains_exclude')))
			{
				$this->globalWhitelistDomainsAppended = true;
			
				$globalWhitelistedDomains = kConf::get("global_whitelisted_domains");
				if(!is_array($globalWhitelistedDomains))
					$globalWhitelistedDomains = explode(',', $globalWhitelistedDomains);
				
				foreach($globalWhitelistedDomains as $globalWhitelistedDomain)
					$this->values[] = new kStringValue($globalWhitelistedDomain);
			}
		}

		kApiCache::addExtraField(kApiCache::ECF_REFERRER, kApiCache::COND_SITE_MATCH, $this->getStringValues($scope));
		
		return parent::internalFulfilled($scope);
	}
	
	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		return ($field === $value) || (strpos($field, ".".$value) !== false);
	}

	/* (non-PHPdoc)
	 * @see kCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}
