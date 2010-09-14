<?php

/**
 * Subclass for performing query and update operations on the 'bulk_upload_result' table.
 *
 * 
 *
 * @package lib.model
 */ 
class BulkUploadResultPeer extends BaseBulkUploadResultPeer
{
	public static function retrieveByBulkUploadId($bulkUploadId)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		
		return self::doSelect($criteria);
	}
	
	
	/**
	 * @return BulkUploadResult 
	 */
	public static function retrieveByEntryId($entryId, $bulkUploadId = null)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::ENTRY_ID, $entryId);
		if($bulkUploadId)
			$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		
		return self::doSelectOne($criteria);
	}
	
	/**
	 * @return BulkUploadResult 
	 */
	public static function retrieveLastByBulkUploadId($bulkUploadId)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		$criteria->addDescendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		
		return self::doSelectOne($criteria);
	}
	
	public static function retrieveWithEntryByBulkUploadId($bulkUploadId)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		$criteria->add(BulkUploadResultPeer::ENTRY_ID, null, Criteria::ISNOTNULL);
		$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		
		return self::doSelect($criteria);
	}
}
