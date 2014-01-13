<?php
/**
 * Pending media entry object, represents a media entry that waits for live segments to be ready 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kPendingMediaEntry
{
	/**
	 * @var string
	 */
	protected $entryId;
	
	/**
	 * @var int
	 */
	protected $dc;
	
	/**
	 * @var float
	 */
	protected $requiredDuration;
	
	public function __construct($entryId, $dc, $requiredDuration)
	{
		$this->entryId = $entryId;
		$this->dc = $dc;
		$this->requiredDuration = $requiredDuration;
	}
	
	/**
	 * @return float $requiredDuration
	 */
	public function getRequiredDuration()
	{
		return $this->requiredDuration;
	}

	/**
	 * @return string $entryId
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @return int $dc
	 */
	public function getDc()
	{
		return $this->dc;
	}
}