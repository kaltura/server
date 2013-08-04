<?php
/**
 * @package Core
 * @subpackage model.data
 * @abstract
 */
class kRule 
{
	/**
	 * Conditions to validate the rule
	 * No conditions means always apply
	 * 
	 * @var array<kCondition>
	 */
	protected $conditions;
	
	/**
	 * @var accessControl
	 */
	protected $accessControl;
	
	/**
	 * Message to be thrown to the player in case the rule 

	 * 
	 * @var string
	 */
	protected $message;
	
	/**
	 * Actions to be performed by the player in case the rule fulfilled
	 * 
	 * @var array<kAccessControlAction>
	 */
	protected $actions;
	
	/**
	 * Indicates what contexts should be tested by this rule 
	 * No contexts means any context
	 * 
	 * @var array of accessControlContextType
	 */
	protected $contexts;
	
	/**
	 * Indicates that this rule is enough and no need to continue checking the rest of the rules 
	 * 
	 * @var bool
	 */
	protected $stopProcessing;
	
	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		$this->accessControl = $accessControl;
	}
	
	/**
	 * @return the $conditions
	 */
	public function getConditions() 
	{
		return $this->conditions;
	}

	/**
	 * @param array<kCondition> $conditions
	 */
	public function setConditions($conditions) 
	{
		$this->conditions = $conditions;
	}

	/**
	 * @return bool
	 */
	protected function isInContext()
	{
		if(!is_array($this->contexts) || !count($this->contexts))
			return true;
			
		$scope = $this->accessControl->getScope();
		foreach($this->contexts as $context)
			if($scope->isInContext($context))
				return true;
				
		return false;
	}

	/**
	 * @return bool
	 */
	protected function fulfilled()
	{
		if(!$this->isInContext())
		{
			KalturaLog::debug("Rule is not in context");
			return false;
		}
			
		if(!is_array($this->conditions) || !count($this->conditions))
		{
			KalturaLog::debug("No conditions found");
			return true;
		}
			
		foreach($this->conditions as $condition)
		{
			if(!$condition->fulfilled($this->accessControl->getScope()))
			{
				KalturaLog::debug("Condition [" . get_class($condition) . "] not  fulfilled");
				return false;
			}
		}
				
		KalturaLog::debug("All conditions fulfilled");
		return true;
	}	
	
	/**
	 * @return bool
	 */
	public function shouldDisableCache()
	{	
		if(!$this->isInContext())
			return false;
			
		if(!is_array($this->conditions))
			return true;
		
		foreach ($this->conditions as $condition)
		{
			if ($condition->shouldDisableCache($this->accessControl->getScope()))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param kEntryContextDataResult $context
	 * @return boolean
	 */
	public function applyContext(kEntryContextDataResult $context)
	{
		if(!$this->fulfilled())
		{
			KalturaLog::debug("Rule conditions NOT fulfilled");
			return false;
		}
			
		KalturaLog::debug("Rule conditions fulfilled");
		if ($this->message)
		{
			$context->addAccessControlMessage($this->message);
		}
		
		if(is_array($this->actions))
		{
			foreach($this->actions as $action)
				$context->addAccessControlAction($action);
		}
				
		return true;
	}
	
	/**
	 * @return string message
	 */
	public function getMessage() 
	{
		return $this->message;
	}

	/**
	 * @return array<kAccessControlAction>
	 */
	public function getActions() 
	{
		return $this->actions;
	}

	/**
	 * @return array of accessControlContextType
	 */
	public function getContexts() 
	{
		return $this->contexts;
	}

	/**
	 * @return bool stop processing
	 */
	public function getStopProcessing() 
	{
		return $this->stopProcessing;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) 
	{
		$this->message = $message;
	}

	/**
	 * @param array<kAccessControlAction> $actions
	 */
	public function setActions(array $actions) 
	{
		$this->actions = $actions;
	}

	/**
	 * @param array $contexts of accessControlContextType
	 */
	public function setContexts(array $contexts) 
	{
		$this->contexts = $contexts;
	}

	/**
	 * @param bool $stopProcessing
	 */
	public function setStopProcessing($stopProcessing) 
	{
		$this->stopProcessing = $stopProcessing;
	}
	
	/**
	 * @param accessControl $accessControl
	 */
	public function setAccessControl($accessControl) 
	{
		$this->accessControl = $accessControl;
	}
	
	public function __sleep()
	{
		$vars = get_class_vars('kRule');
		unset($vars['accessControl']);
		return array_keys($vars);
	}
}
