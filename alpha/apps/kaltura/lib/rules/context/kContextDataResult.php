<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kContextDataResult 
{
	/**
	 * Array of messages as received from the invalidated rules 
	 * @var array<string>
	 */
	private $messages = array();
	
	/**
	 * Array of actions as received from the invalidated rules
	 * @var array<kRuleAction>
	 */
	private $actions = array();
	
	/**
	 * @return array<string>
	 */
	public function getMessages() 
	{
		return $this->messages;
	}

	/**
	 * @return array<kRuleAction>
	 */
	public function getActions() 
	{
		return $this->actions;
	}

	/**
	 * @param string $message
	 */
	public function addMessage($message) 
	{
		$this->messages[] = $message;
	}

	/**
	 * @param kRuleAction $action
	 */
	public function addAction(kRuleAction $action) 
	{
		$this->actions[] = $action;
	}
	
}
