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
	public function applyContext(kEntryContextDataResult $context)
	{
		$fulfilled = parent::applyContext($context);
		if(!$fulfilled)
			$context->setPreviewLength($this->getAction()->getLimit());
			
		return $fulfilled;
	}

	/**
	 * @return kSiteCondition
	 */
	private function getCondition()
	{
		if(!$this->condition && count($this->getConditions()))
			$this->condition = reset($this->getConditions());
			
		return $this->condition;
	}

	/**
	 * @return kAccessControlPreviewAction
	 */
	private function getAction()
	{
		if(!$this->action && count($this->getActions()))
			$this->action = reset($this->getActions());
			
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

