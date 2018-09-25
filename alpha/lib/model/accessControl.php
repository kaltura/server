<?php

/**
 * Subclass for representing a row from the 'access_control' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class accessControl extends BaseaccessControl implements IBaseObject
{
	/**
	 * True when set as partner default (saved on partner object)
	 * 
	 * @var bool
	 */
	protected $isDefault;
	
	/**
	 * @var accessControlScope
	 */
	protected $scope;
	
	
	const IP_ADDRESS_RESTRICTION_COLUMN_NAME = 'ip_address_restriction';
	const USER_AGENT_RESTRICTION_COLUMN_NAME = 'user_agent_restriction';
	const CUSTOM_DATA_RULES_ARRAY_COMPRESSED = 'rules_array_compressed';
	const CUSTOM_DATA_IP_TREE = 'ip_tree';
	
	/* (non-PHPdoc)
	 * @see BaseaccessControl::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if ($this->isColumnModified(accessControlPeer::DELETED_AT))
		{
			if ($this->isDefault === true)
				throw new kCoreException("Default access control profile [" . $this->getId(). "] can't be deleted", kCoreException::ACCESS_CONTROL_CANNOT_DELETE_PARTNER_DEFAULT);
				
			$defaultAccessControl = $this->getPartner()->getDefaultAccessControlId();
			if (!$defaultAccessControl)
				throw new kCoreException("no default access control on partner",kCoreException::NO_DEFAULT_ACCESS_CONTROL);
			
			entryPeer::updateAccessControl($this->getPartnerId(), $this->id, $defaultAccessControl);
		}
		
		return parent::preSave($con);
	}

	/* (non-PHPdoc)
	 * @see BaseaccessControl::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$c = new Criteria();
		$c->add(accessControlPeer::PARTNER_ID, $this->getPartnerId());
		$count = accessControlPeer::doCount($c);
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		$maxAccessControls = $partner->getAccessControls();
		if ($count >= $maxAccessControls)
			throw new kCoreException("Max number of access control profiles [$maxAccessControls] was reached", kCoreException::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED, $maxAccessControls);
		
		return parent::preInsert($con);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BaseaccessControl#save()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isColumnModified(accessControlPeer::RULES)) {
			$this->setIpTree($this->buildRulesIpTree());
		}

		// set this profile as partners default
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		if ($partner && $this->isDefault === true && $partner->getDefaultAccessControlId() !== $this->getId())
		{
			$partner->setDefaultAccessControlId($this->getId());
			$partner->save();
		}
		
		return parent::save($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseaccessControl#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(accessControlPeer::DELETED_AT) && !is_null($this->getDeletedAt()))
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see BaseaccessControl::copyInto()
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		/* @var $copyObj accessControl */
		parent::copyInto($copyObj, $deepCopy);
		$copyObj->setIsDefault($this->getIsDefault());
	}
	
	/**
	 * Set the accessControlScope, called internally only
	 * 
	 * @param $scope
	 */
	protected function setScope(accessControlScope $scope)
	{
		$this->scope = $scope;
	}
	
	/**
	 * Get the accessControlScope
	 * 
	 * @return accessControlScope
	 */
	public function &getScope()
	{
		if (!$this->scope)
			$this->scope = new accessControlScope();
			
		return $this->scope;
	}
	
	/**
	 * Check if there are any rules in this accessControl object
	 * 
	 * @return boolean
	 */
	public function hasRules($contextType = null)
	{
		$rules = $this->getRulesArray();
		if (is_null($contextType))
			return count($rules) ? true : false;
		
		foreach($rules as $rule)
		{
			/* @var $rule kRule */
			$contexts = $rule->getContexts();
			if(!is_array($contexts) || !count($contexts))
				return true;
			
			if (in_array($contextType, $contexts))
				return true;
		}
		return false;
	}
	
	/**
	 * @param kEntryContextDataResult $context
	 * @param accessControlScope $scope
	 * @return boolean disable cache or not
	 */
	public function applyContext(kEntryContextDataResult &$context, accessControlScope $scope = null)
	{
		if($scope)
			$this->setScope($scope);

		$disableCache = false;
		$isKsAdmin = $this->scope && $this->scope->getKs() && $this->scope->getKs()->isAdmin();
		
		$rules = $this->getRulesArray();
		
		// in case of an IP optimization tree filter relevant rules
		$ipTree = $this->getIpTree();
		if ($ipTree)
		{
			// get the ip the tree was optimized for
			$header = $ipTree['header'];
			$acceptInternalIps = $ipTree['acceptInternalIps'];
			$ip = infraRequestUtils::getIpFromHttpHeader($header, $acceptInternalIps, true);

			// find relevant rules and add the rules the tree didn't optimize
			$values = kIpAddressUtils::filterTreeByIp($ip, $ipTree['tree']);
			
			$filteredRules = array();
			foreach($values as $value)
			{
				foreach(explode(',', $value) as $ruleCond)
				{
					list($rule, $cond) = explode(':', $ruleCond);

					if (!isset($filteredRules[$rule]))
					{
						$filteredRules[$rule] = array();
					}

					$filteredRules[$rule][] = $cond;
				}
			}

			$filteredRules += $ipTree['unfiltered'];

			$newRules = array();
			foreach($filteredRules as $filteredRule => $filteredConds) {
				$rule = $rules[$filteredRule];
				
				// remove conditions which were already found in the ipTree
				if (is_array($filteredConds)) {
					$rule->setConditions(array_diff_key($rule->getConditions(), $filteredConds));
				}
				
				$newRules[] = $rules[$filteredRule];
			}

			$rules = $newRules;

			// since there are many ip related caching rules, cache the response only for this specific ip
			kApiCache::addExtraField(array('type' => kApiCache::ECF_IP,
				kApiCache::ECFD_IP_HTTP_HEADER => $header,
				kApiCache::ECFD_IP_ACCEPT_INTERNAL_IPS => $acceptInternalIps),
				kApiCache::COND_IP_RANGE, array($ip));
		}
		
		foreach($rules as $rule)
		{
			if($isKsAdmin && !$rule->getForceAdminValidation())
				continue;
				
			/* @var $rule kRule */
			$fulfilled = $rule->applyContext($context);
				 
			if($rule->shouldDisableCache())
				$disableCache = true;
				
			if($fulfilled && $rule->getStopProcessing())
				break;
		}
			
		return $disableCache;
	}
	
	/**
	 * @param array<kRule> $rules
	 */
	public function setRulesArray(array $rules)
	{
		$serializedRulesArray = serialize($rules);
		
		if(strlen($serializedRulesArray) > myCustomData::MAX_TEXT_FIELD_SIZE)
		{
			$this->setRulesArrayCompressed(true);
			$serializedRulesArray = gzcompress($serializedRulesArray);
			if(strlen(utf8_encode($serializedRulesArray)) > myCustomData::MAX_MEDIUM_TEXT_FIELD_SIZE)
				throw new kCoreException('Exceeded max size allowed for access control', kCoreException::EXCEEDED_MAX_CUSTOM_DATA_SIZE);
				
		}
		else 
		{
			$this->setRulesArrayCompressed(false);
		}
		
		$this->setRules($serializedRulesArray);
	}
	
	public function setRulesArrayCompressed($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_RULES_ARRAY_COMPRESSED, $v);
	}
	
	public function getRulesArrayCompressed()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_RULES_ARRAY_COMPRESSED, null, false);
	}

	public function setIpTree($v) {
		$this->putInCustomData(self::CUSTOM_DATA_IP_TREE, $v ? gzcompress(json_encode($v)) : null);
	}

	public function getIpTree() {
		$s = $this->getFromCustomData(self::CUSTOM_DATA_IP_TREE, null, false);
		if ($s) {
			return json_decode(gzuncompress($s), true);
		}
	}
	
	/**
	 * @return array<kRule>
	 */
	public function getRulesArray($migrate = false)
	{
		$rules = array();
		$rulesString = $this->getRules();
		if($rulesString && !$migrate)
		{
			try
			{
				if($this->getRulesArrayCompressed())
					$rulesString = gzuncompress($rulesString);
				
				$rules = unserialize($rulesString);
			}
			catch(Exception $e)
			{
				KalturaLog::err("Unable to unserialize [$rulesString], " . $e->getMessage());
				$rules = array();
			}
		} 
		
		// TODO - remove after full migration
		if(is_null($rulesString) || $migrate)
		{
			if (!is_null($this->getSiteRestrictType()))
				$rules[] = new kAccessControlSiteRestriction($this);
				
			if (!is_null($this->getCountryRestrictType()))
				$rules[] = new kAccessControlCountryRestriction($this);
				
			if (!is_null($this->getKsRestrictPrivilege()))
			{
				if($this->getPrvRestrictPrivilege())
					$rules[] = new kAccessControlPreviewRestriction($this);
				else
					$rules[] = new kAccessControlSessionRestriction($this);
			}
				
			if (!is_null($this->getFromCustomData(self::IP_ADDRESS_RESTRICTION_COLUMN_NAME)))
				$rules[] = new kAccessControlIpAddressRestriction($this);
				
			if (!is_null($this->getFromCustomData(self::USER_AGENT_RESTRICTION_COLUMN_NAME)))
				$rules[] = new kAccessControlUserAgentRestriction($this);
		}
		
		foreach ($rules as &$rule)
			$rule->setScope($this->getScope());
			
		return $rules;
	}
	
	/**
	 * @param bool $v
	 */
	public function setIsDefault($v)
	{
		$this->isDefault = (bool)$v;
	}
	
	/**
	 * @return boolean
	 */
	public function getIsDefault()
	{
		if ($this->isDefault === null)
		{
			if ($this->isNew())
				return false;
				
			$partner = PartnerPeer::retrieveByPK($this->partner_id);
			if ($partner && ($this->getId() == $partner->getDefaultAccessControlId()))
				$this->isDefault = true;
			else
				$this->isDefault = false;
		}
		
		return $this->isDefault;
	}

	public function getPartner()    { return PartnerPeer::retrieveByPK( $this->getPartnerId() ); }
	
	public function getCacheInvalidationKeys()
	{
		return array("accessControl:id=".strtolower($this->getId()));
	}

	/**
	 * Build a binary tree of IPs based on a given access control rules array
	 * Filtered rules are rules which contain a kIpAddressCondition without NOT set
	 * and with the most common ip type (internal / specific header + accept internal ips).
	 * In case of an eCDN with many rules The optimization gain is substantial.
	 * The non filtered rules will be matched as in the regular flow
	 * 
	 * @param array<kRule> $rules
	 * 
	 * @return array
	 */
	public function buildRulesIpTree()
	{
		$rules = $this->getRulesArray();
		
		$unfilteredRules = array();
		$ipTree = array();
		$rulesIpTree = array('unfiltered' => &$unfilteredRules, 'tree' => &$ipTree);

		// find most common ip cond type rule (internal / specific header + accept internal ips)
		$ipCondTypes = array();
		$largestCondType = false;
		
		for($ruleNum = 0; $ruleNum < count($rules); $ruleNum++)
		{
			$rule = $rules[$ruleNum];
			$conditions = $rule->getConditions();
			for($condNum = 0; $condNum < count($conditions); $condNum++)
			{
				$condition = $conditions[$condNum];
				if (!$condition->getNot() && $condition instanceof kIpAddressCondition)
				{
					$key = $condition->getHttpHeader() . ',' . $condition->getAcceptInternalIps();
					if (!isset($ipCondTypes[$key])) {
						$ipCondTypes[$key] = 1;
					}
					else {
						$ipCondTypes[$key]++;
					}

					if (!$largestCondType || $ipCondTypes[$key] > $ipCondTypes[$largestCondType]) {
						$largestCondType = $key;
					}
				}
			}
		}
		
		// don't bother with building the ip tree for a small number of conditions
		if ($largestCondType === false || $ipCondTypes[$largestCondType] < 100)
			return null;
		
		// build tree from most common ip cond type conditions
		
		for($ruleNum = 0; $ruleNum < count($rules); $ruleNum++)
		{
			$rule = $rules[$ruleNum];
			$conditions = $rule->getConditions();
			$filtered = false;
			for($condNum = 0; $condNum < count($conditions); $condNum++)
			{
				$condition = $conditions[$condNum];
				if ($condition->getNot() || !($condition instanceof kIpAddressCondition)) {
					continue;
				}
				
				$key = $condition->getHttpHeader() . ',' . $condition->getAcceptInternalIps();
				if ($key != $largestCondType)
				{
					continue;
				}

				$filtered = true;
				$ruleCondNum = "$ruleNum:$condNum";

				kIpAddressUtils::insertRangesIntoIpTree(&$ipTree, $condition->getStringValues(null), $ruleCondNum);
			}
			if (!$filtered) {
					$unfilteredRules[$ruleNum] = true;
			}
		}
		
		if ($largestCondType) {
			list($rulesIpTree['header'], $rulesIpTree['acceptInternalIps']) = explode(',', $largestCondType);
		}
			
		return $rulesIpTree;
	}
}