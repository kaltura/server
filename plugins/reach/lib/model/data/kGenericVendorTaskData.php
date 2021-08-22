
<?php

/**
 * Define vendor task data
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kGenericVendorTaskData extends kVendorTaskData
{
	/**
	 * @var int
	 */
	public $entryDuration;
	
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
}
