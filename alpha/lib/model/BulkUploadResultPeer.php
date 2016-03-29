<?php

/**
 * Subclass for performing query and update operations on the 'bulk_upload_result' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class BulkUploadResultPeer extends BaseBulkUploadResultPeer
{
    protected static $class_types_cache = array(
        BulkUploadObjectType::ENTRY => 'BulkUploadResultEntry',
        BulkUploadObjectType::CATEGORY => 'BulkUploadResultCategory',
        BulkUploadObjectType::USER => 'BulkUploadResultKuser',
        BulkUploadObjectType::CATEGORY_USER => 'BulkUploadResultCategoryKuser',
    );
    
	/**
	 * @param int $bulkUploadId
	 * @param int $index
	 * @return BulkUploadResult
	 */
	public static function retrieveByBulkUploadIdAndIndex($bulkUploadId, $index)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		$criteria->add(BulkUploadResultPeer::LINE_INDEX, $index);
		
		return self::doSelectOne($criteria);
	}
	
	
	
	/**
	 * Retrieve BulkUploadResult object by objectId and bulkUpload job ID.
	 * @param string $objectId
	 * @param int $objectType default value 1 (ENTRY)
	 * @param string $bulkUploadId
	 * @return BulkUploadResult
	 */
	public static function retrieveByObjectId ($objectId, $objectType = 1, $bulkUploadId = null)
	{
	    $criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::OBJECT_ID, $objectId);
		$criteria->add(BulkUploadResultPeer::OBJECT_TYPE, $objectType);
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
	/**
	 * function to retrieve the number of BulkUploadResults with type ENTRY,and objectId<>null
	 * @param int $bulkUploadId
	 * @return int
	 */
	public static function countWithEntryByBulkUploadId($bulkUploadId)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		$criteria->add(BulkUploadResultPeer::OBJECT_ID, null, Criteria::ISNOTNULL);
		$criteria->add(BulkUploadResultPeer::OBJECT_TYPE, BulkUploadObjectType::ENTRY);
		
		return self::doCount($criteria);
	}
	
	/**
	 * Function counts amount of bulk upload results for a given bulk upload job ID and object type
	 * @param int $bulkUploadJobId
	 * @param int $bulkUploadObjectType
	 * @return int
	 */
	public static function countWithObjectTypeByBulkUploadId ($bulkUploadJobId, $bulkUploadObjectType)
	{
	    $criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadJobId);
		//$criteria->add(BulkUploadResultPeer::OBJECT_ID, null, Criteria::ISNOTNULL);
		$criteria->add(BulkUploadResultPeer::OBJECT_TYPE, $bulkUploadObjectType);
		$criteria->add(BulkUploadResultPeer::STATUS, BulkUploadResultStatus::ERROR, Criteria::NOT_EQUAL);
		
		return self::doCount($criteria);
	}
	
	/**
	 * Function counts amount of error bulk upload results for a given bulk upload job ID and object type
	 * @param int $bulkUploadJobId
	 * @param int $bulkUploadObjectType
	 * @return int
	 */
	public static function countErrorWithObjectTypeByBulkUploadId ($bulkUploadJobId, $bulkUploadObjectType)
	{
	    $criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadJobId);
		$criteria->add(BulkUploadResultPeer::OBJECT_TYPE, $bulkUploadObjectType);
		$criteria->add(BulkUploadResultPeer::STATUS, BulkUploadResultStatus::ERROR, Criteria::EQUAL);
		
		return self::doCount($criteria);
	}
	
	/**
	 * function to retrieve the BulkUploadResults with type ENTRY,and objectId<>null
	 * @param int $bulkUploadId
	 * @return array
	 */
	public static function retrieveWithEntryByBulkUploadId($bulkUploadId)
	{
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $bulkUploadId);
		$criteria->add(BulkUploadResultPeer::OBJECT_ID, null, Criteria::ISNOTNULL);
		$criteria->add(BulkUploadResultPeer::OBJECT_TYPE, BulkUploadObjectType::ENTRY);
		$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		
		return self::doSelect($criteria);
	}
	
/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(BulkUploadResultPeer::OBJECT_TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$bulkUploadReultObjectType = $row[$typeField];
			if(isset(self::$class_types_cache[$bulkUploadReultObjectType]))
				return self::$class_types_cache[$bulkUploadReultObjectType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $bulkUploadReultObjectType);
			if($extendedCls)
			{
				self::$class_types_cache[$bulkUploadReultObjectType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$bulkUploadReultObjectType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
}
