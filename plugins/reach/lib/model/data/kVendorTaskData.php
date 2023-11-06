
<?php

/**
 * Define vendor task data
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kVendorTaskData
{
	/**
	 * @var int
	 */
	public $entryDuration;

	/**
	 * @var int
	 */
	public $processedEntryDuration;
	
	/**
	 * @return the $entryDuration
	 */
	public function getEntryDuration()
	{
		return $this->entryDuration;
	}
	
	/**
	 * @param int $entryDuration
	 */
	public function setEntryDuration($entryDuration)
	{
		$this->entryDuration = $entryDuration;
	}

	public function getProcessedEntryDuration()
	{
		return $this->processedEntryDuration;
	}

	/**
	 * @param int $processedEntryDuration
	 */
	public function setProcessedEntryDuration($processedEntryDuration)
	{
		$this->processedEntryDuration = $processedEntryDuration;
	}
}
