<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kBulkUpdateUserEntryData extends kJobData
{
	/**
	 * @var int
	 */
	protected $partnerId;

	/**
	 * @var string
	 */
	protected $entryId;

	/**
	 * @var int
	 */
	protected $oldStatus;

	/**
	 * @var int
	 */
	protected $newStatus;

	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}

	public function getPartnerId()
	{
		return $this->partnerId;
	}

	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}

	public function getEntryId()
	{
		return $this->entryId;
	}

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
