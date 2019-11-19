<?php
/**
 * Subclass representing an entry-related rating count
 *
 *
 *
 * @package Core
 * @subpackage model
 */

class RatingCount
{
	/**
	 * @var string
	 */
	protected $entryId;
	
	/**
	 * @var int
	 */
	protected $rank;
	
	/**
	 * @var int
	 */
	protected $count;
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}
	
	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}
	
	/**
	 * @return int
	 */
	public function getRank()
	{
		return $this->rank;
	}
	
	/**
	 * @param int $rank
	 */
	public function setRank($rank)
	{
		$this->rank = $rank;
	}
	
	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->count;
	}
	
	/**
	 * @param int $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}
	
}