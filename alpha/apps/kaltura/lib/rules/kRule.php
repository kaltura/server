<?php
/**
 * @package Core
 * @subpackage model.data
 * @abstract
 */
class kRule 
{
	/**
	 * Short Rule Description
	 *
	 * @var string
	 */
	public $description;
	
	/**
	 * Rule Custom Data to allow saving rule specific information
	 *
	 * @var string
	 */
	public $ruleData;
	
	/**
	 * Conditions to validate the rule
	 * No conditions means always apply
	 * 
	 * @var array<kCondition>
	 */
	protected $conditions;
	
	/**
	 * Message to be thrown to the player in case the rule 

	 * 
	 * @var string
	 */
	protected $message;


	/**
	 * Rule code to be thrown to the player in case the rule
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * Actions to be performed by the player in case the rule fulfilled
	 * 
	 * @var array<kRuleAction>
	 */
	protected $actions;
	
	/**
	 * Indicates what contexts should be tested by this rule 
	 * No contexts means any context
	 * 
	 * @var array of ContextType
	 */
	protected $contexts;
	
	/**
	 * Indicates that this rule is enough and no need to continue checking the rest of the rules 
	 * 
	 * @var bool
	 */
	protected $stopProcessing;
	
	/**
	 * Indicates if we should force ks validation for admin ks users as well
	 *
	 * @var bool
	 */
	protected $forceAdminValidation;
	
	/**
	 * Indicates the scope on which the rule is applied
	 * 
	 * @var kScope
	 */
	
	protected $scope;
	
	/**
	 * @param kScope $scope
	 */
	public function __construct(kScope $scope = null)
	{
		$this->scope = $scope;
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
			
		foreach($this->contexts as $context)
			if($this->scope->isInContext($context))
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
			if(!$condition->fulfilled($this->scope))
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
			if ($condition->shouldDisableCache($this->scope))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param kContextDataResult $context
	 * @return boolean
	 */
	public function applyContext(kContextDataResult $context)
	{
		if(!$this->fulfilled())
		{
			KalturaLog::debug("Rule conditions NOT fulfilled");
			return false;
		}
			
		KalturaLog::debug("Rule conditions fulfilled");
		if ($this->message)
		{
			$context->addMessage($this->message);

			if ($context->shouldHandleRuleCodes())
				$context->addCodeAndMessage($this->message, $this->code);
		}
		
		if(is_array($this->actions))
		{
			foreach ($this->actions as $action)
			{
				$context->addAction($action);
			}
		}

		return true;
	}
	
	/**
	 * @return string Description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @return string ruleData
	 */
	public function getRuleData()
	{
		return $this->ruleData;
	}

	/**
	 * @return string message
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string code
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return array<kRuleAction>
	 */
	public function getActions() 
	{
		return $this->actions;
	}

	/**
	 * @return array of ContextType
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
	 * @return string Description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string ruleData
	 */
	public function setRuleData($ruleData)
	{
		$this->ruleData = $ruleData;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) 
	{
		$this->message = $message;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * @param array<kRuleAction> $actions
	 */
	public function setActions(array $actions) 
	{
		$this->actions = $actions;
	}

	/**
	 * @param array $contexts of ContextType
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
	 * @param bool $forceAdminValidation
	 */
	public function setForceAdminValidation($forceAdminValidation)
	{
		$this->forceAdminValidation = $forceAdminValidation;
	}
	
	/**
	 * @return bool for validation while using Admin KS
	 */
	public function getForceAdminValidation()
	{
		if(isset($this->forceAdminValidation))
			return $this->forceAdminValidation;
		
		return false;
	}
	
	/**
	 * @param kScope $scope
	 */
	public function setScope($scope) 
	{
		$this->scope = $scope;
	}
	
	public function __sleep()
	{
		$vars = get_class_vars('kRule');
		unset($vars['scope']);
		return array_keys($vars);
	}
}
