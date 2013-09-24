<?php
/**
 * @package Core
 * @subpackage model.data
 * 
 * Old session restriction for backward compatibility
 */
class kAccessControlSessionRestriction extends kAccessControlRestriction
{
	/**
	 * @var kAuthenticatedCondition
	 */
	private $condition;
	
	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		parent::__construct($accessControl);
		$this->setActions(array(new kAccessControlAction(RuleActionType::BLOCK)));
		
		$this->condition = new kAuthenticatedCondition(true);
		if($accessControl)
			$this->condition->setPrivileges(array($accessControl->getKsRestrictPrivilege()));
		
		$this->setConditions(array($this->getCondition()));
	}

	/* (non-PHPdoc)
	 * @see kRule::applyContext()
	 */
	public function applyContext(kContextDataResult $context)
	{
		$fulfilled = parent::applyContext($context);
		if($fulfilled)
			$context->setIsSessionRestricted(true);
			
		return $fulfilled;
	}

	/**
	 * @return kAuthenticatedCondition
	 */
	private function getCondition()
	{
		$conditions = $this->getConditions();
		if(!$this->condition && count($conditions))
			$this->condition = reset($conditions);
			
		return $this->condition;
	}
}

