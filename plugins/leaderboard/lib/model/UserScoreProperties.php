<?php

/**
 * @package plugins.leaderboard
 * @subpackage model
 */
class UserScoreProperties
{
	/**
	 * @var int
	 */
	protected $rank;
	
	/**
	 * @var string
	 */
	protected $userId;
	
	/**
	 * @var int
	 */
	protected $score;
	
	/**
	 * @var string
	 */
	protected $scoreTags;
	
	/**
	 * @var int
	 */
	protected $oldRank;
	
	
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
	 * @return string
	 */
	public function getUserId()
	{
		return $this->userId;
	}
	
	/**
	 * @param string $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	/**
	 * @return int
	 */
	public function getScore()
	{
		return $this->score;
	}
	
	/**
	 * @param int $score
	 */
	public function setScore($score)
	{
		$this->score = $score;
	}
	
	/**
	 * @return string
	 */
	public function getScoreTags()
	{
		return $this->scoreTags;
	}
	
	/**
	 * @param string $scoreTags
	 */
	public function setScoreTags($scoreTags)
	{
		$this->scoreTags = $scoreTags;
	}
	
	/**
	 * @return int
	 */
	public function getOldRank()
	{
		return $this->oldRank;
	}
	
	/**
	 * @param int $oldRank
	 */
	public function setOldRank($oldRank)
	{
		$this->oldRank = $oldRank;
	}
}