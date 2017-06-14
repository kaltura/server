<?php

class ESearchCaptionItem extends ESearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var int;
	 */
	protected $startTimeInVideo;

	/**
	 * @var int;
	 */
	protected $endTimeInVideo;


	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public function getType()
	{
		return 'caption';
	}

	/**
	 * @return int
	 */
	public function getStartTimeInVideo()
	{
		return $this->startTimeInVideo;
	}

	/**
	 * @param int $startTimeInVideo
	 */
	public function setStartTimeInVideo($startTimeInVideo)
	{
		$this->startTimeInVideo = $startTimeInVideo;
	}

	/**
	 * @return int
	 */
	public function getEndTimeInVideo()
	{
		return $this->endTimeInVideo;
	}

	/**
	 * @param int $endTimeInVideo
	 */
	public function setEndTimeInVideo($endTimeInVideo)
	{
		$this->endTimeInVideo = $endTimeInVideo;
	}


}