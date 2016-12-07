<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPlaybackContextResult {

	/**
	 * @var array<kPlaybackSource>
	 */
	private $sources;

	/**
	 * @var array
	 */
	private $flavorAssets;

	/**
	 * Array of messages as received from the invalidated rules
	 * @var array<string>
	 */
	private $messages;

	/**
	 * Array of actions as received from the invalidated rules
	 * @var array<kRuleAction>
	 */
	private $actions;

	/**
	 * Array of actions as received from the invalidated rules
	 * @var array<kPlaybackRestriction>
	 */
	private $restrictions;


	/**
	 * @return array<kPlaybackSource>
	 */
	public function getSources()
	{
		return $this->sources;
	}

	/**
	 * @param array $sources
	 */
	public function setSources($sources)
	{
		$this->sources = $sources;
	}

	/**
	 * @return array
	 */
	public function getFlavorAssets()
	{
		return $this->flavorAssets;
	}

	/**
	 * @param array $flavorAssets
	 */
	public function setFlavorAssets($flavorAssets)
	{
		$this->flavorAssets = $flavorAssets;
	}

	/**
	 * @return array<string>
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * @param array $messages
	 */
	public function setMessages($messages)
	{
		$this->messages = $messages;
	}

	/**
	 * @return array<kRuleAction>
	 */
	public function getActions()
	{
		return $this->actions;
	}

	/**
	 * @param array $actions
	 */
	public function setActions($actions)
	{
		$this->actions = $actions;
	}

	/**
	 * @return array<kPlaybackRestriction>
	 */
	public function getRestrictions()
	{
		return $this->restrictions;
	}

	/**
	 * @param array $restrictions
	 */
	public function setRestrictions($restrictions)
	{
		$this->restrictions = $restrictions;
	}



}