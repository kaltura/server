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
	
	/**
	 * @var float
	 */
	protected $offset;
	
	/**
	 * @var float
	 */
	protected $duration;
	
	public function __construct($entryId, $dc, $requiredDuration, $offset, $duration)
	{
		$this->entryId = $entryId;
		$this->dc = $dc;
		$this->requiredDuration = $requiredDuration;
		$this->offset = $offset;
		$this->duration = $duration;
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
	
	/**
	 * @return the $offset
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @return the $duration
	 */
	public function getDuration() {
		return $this->duration;
	}
}