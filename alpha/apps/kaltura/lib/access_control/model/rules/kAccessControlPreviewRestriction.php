<?php
/**
 * @package Core
 * @subpackage model.data
 * 
 * Old preview restriction for backward compatibility
 */
class kAccessControlPreviewRestriction extends kAccessControlRestriction
{
	/**
	 * @var kAuthenticatedCondition
	 */
	private $condition;
	
	/**
	 * @var kAccessControlPreviewAction
	 */
	private $action;
	
	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		parent::__construct($accessControl);
		
		$this->action = new kAccessControlPreviewAction();
		$this->condition = new kAuthenticatedCondition(true);
		if($accessControl)
		{
			$this->getCondition()->setPrivileges(array($accessControl->getPrvRestrictPrivilege()));
			$this->setPreviewLength($accessControl->getPrvRestrictLength());
		}
		
		$this->setActions(array($this->getAction()));
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
			
		// this is not a mistake, although it may looked like one, it should be set even in the condition is not fulfilled.
		$context->setPreviewLength($this->getAction()->getLimit());
			
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
	 * @return kAccessControlPreviewAction
	 */
	private function getAction()
	{
		$actions = $this->getActions();
		if(!$this->action && count($actions))
			$this->action = reset($actions);
			
		return $this->action;
	}
	

	/**
	 * @param int $previewLength
	 */
	function setPreviewLength($previewLength)
	{
		$this->getAction()->setLimit($previewLength);
	}
	
	/**
	 * @return int
	 */
	function getPreviewLength()
	{
		return $this->getAction()->getLimit();	
	}
}

