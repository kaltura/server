<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kUpdateUserEntriesData extends kJobData
{
	/**
	 * @var int
	 */
	protected $oldStatus;

	/**
	 * @var int
	 */
	protected $newStatus;

	public function setOldStatus($oldStatus)
	{
		$this->oldStatus = $oldStatus;
	}

	public function getOldStatus()
	{
		return $this->oldStatus;
	}

	public function setNewStatus($newStatus)
	{
		$this->newStatus = $newStatus;
	}

	public function getNewStatus()
	{
		return $this->newStatus;
	}
}
