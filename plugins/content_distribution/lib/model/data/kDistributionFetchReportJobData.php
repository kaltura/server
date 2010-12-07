<?php
class kDistributionFetchReportJobData extends kDistributionJobData
{
	/**
	 * @var int
	 */
	private $plays;
	
	/**
	 * @var int
	 */
	private $views;
	
	/**
	 * @return the $plays
	 */
	public function getPlays()
	{
		return $this->plays;
	}

	/**
	 * @return the $views
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * @param int $plays
	 */
	public function setPlays($plays)
	{
		$this->plays = $plays;
	}

	/**
	 * @param int $views
	 */
	public function setViews($views)
	{
		$this->views = $views;
	}
}