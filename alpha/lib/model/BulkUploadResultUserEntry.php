<?php
/**
 * Subclass for representing a row from the 'bulk_upload_result' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class BulkUploadResultUserEntry extends BulkUploadResult
{
	const USER_ENTRY_ID = "userEntryId";

	/* (non-PHPdoc)
	 * @see BulkUploadResult::handleRelatedObjects()
	 */
	public function handleRelatedObjects()
	{
		$userEntry = $this->getObject();
		if ($userEntry)
		{
			$userEntry->setBulkUploadId($this->getBulkUploadJobId());
			$userEntry->save();
		}
	}

	/* (non-PHPdoc)
	 * @see BulkUploadResult::getObject()
	 */
	public function getObject()
	{
		return UserEntryPeer::retrieveByPKNoFilter($this->getObjectId());
	}

	//Set properties for user entries

	public function getUserEntryId()	{return $this->getFromCustomData(self::USER_ENTRY_ID);}
	public function setUserEntryId($v)	{$this->putInCustomData(self::USER_ENTRY_ID, $v);}

}