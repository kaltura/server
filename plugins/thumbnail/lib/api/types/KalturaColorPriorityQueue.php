<?php
/**
 * @package plugins.thumbnail
 * @subpackage api.objects
 */
class KalturaColorPriorityQueue extends SplPriorityQueue
{
	protected $maxItems;

	/**
	 * KalturaColorPriorityQueue constructor.
	 * @param $maxItems
	 */
	public function __construct($maxItems)
	{
		if($maxItems < 1)
		{
			$maxItems = 1;
		}

		$this->maxItems = $maxItems;
	}

	/**
	 * @param int $priority1
	 * @param int $priority2
	 * @return int
	 */
	public function compare($priority1, $priority2)
	{
		return $priority2 - $priority1;
	}

	public function insert($value, $priority)
	{
		parent::insert($value, $priority);
		if($this->count() > $this->maxItems)
		{
			$this->extract();
		}
	}
}