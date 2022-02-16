<?php

/**
 * @package plugins.game
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
}