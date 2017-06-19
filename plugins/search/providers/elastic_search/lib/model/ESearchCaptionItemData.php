<?php

class ESearchCaptionItemData extends ESearchItemData
{

	/**
	 * @var string
	 */
	protected $line;

	/**
	 * @var int
	 */
	protected $startsAt;

	/**
	 * @var int
	 */
	protected $endsAt;

	public function getType()
	{
		return 'caption';
	}

	/**
	 * @return string
	 */
	public function getLine()
	{
		return $this->line;
	}

	/**
	 * @param string $line
	 */
	public function setLine($line)
	{
		$this->line = $line;
	}

	/**
	 * @return int
	 */
	public function getStartsAt()
	{
		return $this->startsAt;
	}

	/**
	 * @param int $startsAt
	 */
	public function setStartsAt($startsAt)
	{
		$this->startsAt = $startsAt;
	}

	/**
	 * @return int
	 */
	public function getEndsAt()
	{
		return $this->endsAt;
	}

	/**
	 * @param int $endsAt
	 */
	public function setEndsAt($endsAt)
	{
		$this->endsAt = $endsAt;
	}

	public function loadFromElasticHits($objectResult)
	{
		$this->setLine($objectResult['_source']['content']);
		$this->setStartsAt($objectResult['_source']['start_time']);
		$this->setEndsAt($objectResult['_source']['end_time']);

	}


}