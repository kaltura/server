<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPlaybackContext {

	/**
	 * @var array<kPlaybackSource>
	 */
	protected $sources;

	/**
	 * @var array<kPlaybackCaption>
	 */
	protected $playbackCaptions;

	/**
	 * @var array
	 */
	protected $flavorAssets;

	/**
	 * Array of actions as received from the invalidated rules
	 * @var array<kRuleAction>
	 */
	protected $actions;

	/**
	 * Array of actions as received from the invalidated rules
	 * @var array<kAccessControlMessage>
	 */
	protected $messages;

	/**
	 * @var array
	 */
	protected $bumperData;

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
	 * @return array
	 */
	public function getPlaybackCaptions()
	{
		return $this->playbackCaptions;
	}

	/**
	 * @param array $playbackCaptions
	 */
	public function setPlaybackCaptions($playbackCaptions)
	{
		$this->playbackCaptions = $playbackCaptions;
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
	 * @return array
	 */
	public function getBumperData()
	{
		return $this->bumperData;
	}

	/**
	 * @param array $bumperData
	 */
	public function setBumperData($bumperData)
	{
		$this->bumperData = $bumperData;
	}
}
