<?php
/**
 * @package Core
 * @subpackage model.data
 * 
 * Old site restriction for backward compatibility
 */
class kAccessControlSiteRestriction extends kAccessControlRestriction
{
	/**
	 * @var kSiteCondition
	 */
	private $condition;
	
	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		parent::__construct($accessControl);
		$this->setActions(array(new kAccessControlAction(RuleActionType::BLOCK)));
		
		$this->condition = new kSiteCondition(true);
		if($accessControl)
		{
			$this->setSiteList($accessControl->getSiteRestrictList());
			$this->setSiteRestrictionType($accessControl->getSiteRestrictType());
		}
		
		$this->setConditions(array($this->getCondition()));
	}

	/* (non-PHPdoc)
	 * @see kRule::applyContext()
	 */
	public function applyContext(kContextDataResult $context)
	{
		$fulfilled = parent::applyContext($context);
		if($fulfilled)
			$context->setIsSiteRestricted(true);
			
		return $fulfilled;
	}

	/**
	 * @return kSiteCondition
	 */
	private function getCondition()
	{
		$conditions = $this->getConditions();
		if(!$this->condition && count($conditions))
			$this->condition = reset($conditions);
			
		return $this->condition;
	}

	/**
	 * @param int $type
	 */
	public function setSiteRestrictionType($type)
	{
		$this->getCondition()->setNot($type == kAccessControlRestriction::RESTRICTION_TYPE_ALLOW_LIST);
	}
	
	/**
	 * @param string $siteList
	 */
	public function setSiteList($values)
	{
		$values = explode(',', $values);
		$stringValues = array();
		foreach($values as $value)
			$stringValues[] = new kStringValue($value);
			
		$this->getCondition()->setValues($stringValues);
	}
	
	/**
	 * @return int
	 */
	public function getSiteRestrictionType()
	{
		return $this->getCondition()->getNot() ? kAccessControlRestriction::RESTRICTION_TYPE_ALLOW_LIST : kAccessControlRestriction::RESTRICTION_TYPE_RESTRICT_LIST;	
	}
	
	/**
	 * @return string
	 */
	public function getSiteList()
	{
		return implode(',', $this->getCondition()->getStringValues());
	}
}
