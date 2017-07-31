<?php


/**
 * Skeleton subclass for representing a row from the 'user_entry' table.
 *
 * Describes the relationship between a specific user and a specific entry
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
abstract class UserEntry extends BaseUserEntry {


	/**
	 * UserEntry constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	public function applyDefaultValues()
	{
		$this->setStatus(UserEntryStatus::ACTIVE);
	}

	public function checkAlreadyExists()
	{
		$userEntryCriteria = new Criteria();
		$userEntryCriteria->add(UserEntryPeer::ENTRY_ID, $this->getEntryId());
		$userEntryCriteria->add(UserEntryPeer::KUSER_ID, $this->getKuserId());
		$userEntryCriteria->add(UserEntryPeer::TYPE, $this->getType());
		
		return UserEntryPeer::doSelectOne($userEntryCriteria);
	}

} // UserEntry
