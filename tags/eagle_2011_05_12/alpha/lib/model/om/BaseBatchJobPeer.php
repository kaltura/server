<?php

/**
 * Base static class for performing query and update operations on the 'batch_job' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseBatchJobPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'batch_job';

	/** the related Propel class for this table */
	const OM_CLASS = 'BatchJob';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.BatchJob';

	/** the related TableMap class for this table */
	const TM_CLASS = 'BatchJobTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 42;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'batch_job.ID';

	/** the column name for the JOB_TYPE field */
	const JOB_TYPE = 'batch_job.JOB_TYPE';

	/** the column name for the JOB_SUB_TYPE field */
	const JOB_SUB_TYPE = 'batch_job.JOB_SUB_TYPE';

	/** the column name for the DATA field */
	const DATA = 'batch_job.DATA';

	/** the column name for the FILE_SIZE field */
	const FILE_SIZE = 'batch_job.FILE_SIZE';

	/** the column name for the DUPLICATION_KEY field */
	const DUPLICATION_KEY = 'batch_job.DUPLICATION_KEY';

	/** the column name for the STATUS field */
	const STATUS = 'batch_job.STATUS';

	/** the column name for the ABORT field */
	const ABORT = 'batch_job.ABORT';

	/** the column name for the CHECK_AGAIN_TIMEOUT field */
	const CHECK_AGAIN_TIMEOUT = 'batch_job.CHECK_AGAIN_TIMEOUT';

	/** the column name for the PROGRESS field */
	const PROGRESS = 'batch_job.PROGRESS';

	/** the column name for the MESSAGE field */
	const MESSAGE = 'batch_job.MESSAGE';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'batch_job.DESCRIPTION';

	/** the column name for the UPDATES_COUNT field */
	const UPDATES_COUNT = 'batch_job.UPDATES_COUNT';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'batch_job.CREATED_AT';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'batch_job.CREATED_BY';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'batch_job.UPDATED_AT';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'batch_job.UPDATED_BY';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'batch_job.DELETED_AT';

	/** the column name for the PRIORITY field */
	const PRIORITY = 'batch_job.PRIORITY';

	/** the column name for the WORK_GROUP_ID field */
	const WORK_GROUP_ID = 'batch_job.WORK_GROUP_ID';

	/** the column name for the QUEUE_TIME field */
	const QUEUE_TIME = 'batch_job.QUEUE_TIME';

	/** the column name for the FINISH_TIME field */
	const FINISH_TIME = 'batch_job.FINISH_TIME';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'batch_job.ENTRY_ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'batch_job.PARTNER_ID';

	/** the column name for the SUBP_ID field */
	const SUBP_ID = 'batch_job.SUBP_ID';

	/** the column name for the SCHEDULER_ID field */
	const SCHEDULER_ID = 'batch_job.SCHEDULER_ID';

	/** the column name for the WORKER_ID field */
	const WORKER_ID = 'batch_job.WORKER_ID';

	/** the column name for the BATCH_INDEX field */
	const BATCH_INDEX = 'batch_job.BATCH_INDEX';

	/** the column name for the LAST_SCHEDULER_ID field */
	const LAST_SCHEDULER_ID = 'batch_job.LAST_SCHEDULER_ID';

	/** the column name for the LAST_WORKER_ID field */
	const LAST_WORKER_ID = 'batch_job.LAST_WORKER_ID';

	/** the column name for the LAST_WORKER_REMOTE field */
	const LAST_WORKER_REMOTE = 'batch_job.LAST_WORKER_REMOTE';

	/** the column name for the PROCESSOR_EXPIRATION field */
	const PROCESSOR_EXPIRATION = 'batch_job.PROCESSOR_EXPIRATION';

	/** the column name for the EXECUTION_ATTEMPTS field */
	const EXECUTION_ATTEMPTS = 'batch_job.EXECUTION_ATTEMPTS';

	/** the column name for the LOCK_VERSION field */
	const LOCK_VERSION = 'batch_job.LOCK_VERSION';

	/** the column name for the TWIN_JOB_ID field */
	const TWIN_JOB_ID = 'batch_job.TWIN_JOB_ID';

	/** the column name for the BULK_JOB_ID field */
	const BULK_JOB_ID = 'batch_job.BULK_JOB_ID';

	/** the column name for the ROOT_JOB_ID field */
	const ROOT_JOB_ID = 'batch_job.ROOT_JOB_ID';

	/** the column name for the PARENT_JOB_ID field */
	const PARENT_JOB_ID = 'batch_job.PARENT_JOB_ID';

	/** the column name for the DC field */
	const DC = 'batch_job.DC';

	/** the column name for the ERR_TYPE field */
	const ERR_TYPE = 'batch_job.ERR_TYPE';

	/** the column name for the ERR_NUMBER field */
	const ERR_NUMBER = 'batch_job.ERR_NUMBER';

	/** the column name for the ON_STRESS_DIVERT_TO field */
	const ON_STRESS_DIVERT_TO = 'batch_job.ON_STRESS_DIVERT_TO';

	/**
	 * An identiy map to hold any loaded instances of BatchJob objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array BatchJob[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'JobType', 'JobSubType', 'Data', 'FileSize', 'DuplicationKey', 'Status', 'Abort', 'CheckAgainTimeout', 'Progress', 'Message', 'Description', 'UpdatesCount', 'CreatedAt', 'CreatedBy', 'UpdatedAt', 'UpdatedBy', 'DeletedAt', 'Priority', 'WorkGroupId', 'QueueTime', 'FinishTime', 'EntryId', 'PartnerId', 'SubpId', 'SchedulerId', 'WorkerId', 'BatchIndex', 'LastSchedulerId', 'LastWorkerId', 'LastWorkerRemote', 'ProcessorExpiration', 'ExecutionAttempts', 'LockVersion', 'TwinJobId', 'BulkJobId', 'RootJobId', 'ParentJobId', 'Dc', 'ErrType', 'ErrNumber', 'OnStressDivertTo', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'jobType', 'jobSubType', 'data', 'fileSize', 'duplicationKey', 'status', 'abort', 'checkAgainTimeout', 'progress', 'message', 'description', 'updatesCount', 'createdAt', 'createdBy', 'updatedAt', 'updatedBy', 'deletedAt', 'priority', 'workGroupId', 'queueTime', 'finishTime', 'entryId', 'partnerId', 'subpId', 'schedulerId', 'workerId', 'batchIndex', 'lastSchedulerId', 'lastWorkerId', 'lastWorkerRemote', 'processorExpiration', 'executionAttempts', 'lockVersion', 'twinJobId', 'bulkJobId', 'rootJobId', 'parentJobId', 'dc', 'errType', 'errNumber', 'onStressDivertTo', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::JOB_TYPE, self::JOB_SUB_TYPE, self::DATA, self::FILE_SIZE, self::DUPLICATION_KEY, self::STATUS, self::ABORT, self::CHECK_AGAIN_TIMEOUT, self::PROGRESS, self::MESSAGE, self::DESCRIPTION, self::UPDATES_COUNT, self::CREATED_AT, self::CREATED_BY, self::UPDATED_AT, self::UPDATED_BY, self::DELETED_AT, self::PRIORITY, self::WORK_GROUP_ID, self::QUEUE_TIME, self::FINISH_TIME, self::ENTRY_ID, self::PARTNER_ID, self::SUBP_ID, self::SCHEDULER_ID, self::WORKER_ID, self::BATCH_INDEX, self::LAST_SCHEDULER_ID, self::LAST_WORKER_ID, self::LAST_WORKER_REMOTE, self::PROCESSOR_EXPIRATION, self::EXECUTION_ATTEMPTS, self::LOCK_VERSION, self::TWIN_JOB_ID, self::BULK_JOB_ID, self::ROOT_JOB_ID, self::PARENT_JOB_ID, self::DC, self::ERR_TYPE, self::ERR_NUMBER, self::ON_STRESS_DIVERT_TO, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'job_type', 'job_sub_type', 'data', 'file_size', 'duplication_key', 'status', 'abort', 'check_again_timeout', 'progress', 'message', 'description', 'updates_count', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'priority', 'work_group_id', 'queue_time', 'finish_time', 'entry_id', 'partner_id', 'subp_id', 'scheduler_id', 'worker_id', 'batch_index', 'last_scheduler_id', 'last_worker_id', 'last_worker_remote', 'processor_expiration', 'execution_attempts', 'lock_version', 'twin_job_id', 'bulk_job_id', 'root_job_id', 'parent_job_id', 'dc', 'err_type', 'err_number', 'on_stress_divert_to', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'JobType' => 1, 'JobSubType' => 2, 'Data' => 3, 'FileSize' => 4, 'DuplicationKey' => 5, 'Status' => 6, 'Abort' => 7, 'CheckAgainTimeout' => 8, 'Progress' => 9, 'Message' => 10, 'Description' => 11, 'UpdatesCount' => 12, 'CreatedAt' => 13, 'CreatedBy' => 14, 'UpdatedAt' => 15, 'UpdatedBy' => 16, 'DeletedAt' => 17, 'Priority' => 18, 'WorkGroupId' => 19, 'QueueTime' => 20, 'FinishTime' => 21, 'EntryId' => 22, 'PartnerId' => 23, 'SubpId' => 24, 'SchedulerId' => 25, 'WorkerId' => 26, 'BatchIndex' => 27, 'LastSchedulerId' => 28, 'LastWorkerId' => 29, 'LastWorkerRemote' => 30, 'ProcessorExpiration' => 31, 'ExecutionAttempts' => 32, 'LockVersion' => 33, 'TwinJobId' => 34, 'BulkJobId' => 35, 'RootJobId' => 36, 'ParentJobId' => 37, 'Dc' => 38, 'ErrType' => 39, 'ErrNumber' => 40, 'OnStressDivertTo' => 41, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'jobType' => 1, 'jobSubType' => 2, 'data' => 3, 'fileSize' => 4, 'duplicationKey' => 5, 'status' => 6, 'abort' => 7, 'checkAgainTimeout' => 8, 'progress' => 9, 'message' => 10, 'description' => 11, 'updatesCount' => 12, 'createdAt' => 13, 'createdBy' => 14, 'updatedAt' => 15, 'updatedBy' => 16, 'deletedAt' => 17, 'priority' => 18, 'workGroupId' => 19, 'queueTime' => 20, 'finishTime' => 21, 'entryId' => 22, 'partnerId' => 23, 'subpId' => 24, 'schedulerId' => 25, 'workerId' => 26, 'batchIndex' => 27, 'lastSchedulerId' => 28, 'lastWorkerId' => 29, 'lastWorkerRemote' => 30, 'processorExpiration' => 31, 'executionAttempts' => 32, 'lockVersion' => 33, 'twinJobId' => 34, 'bulkJobId' => 35, 'rootJobId' => 36, 'parentJobId' => 37, 'dc' => 38, 'errType' => 39, 'errNumber' => 40, 'onStressDivertTo' => 41, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::JOB_TYPE => 1, self::JOB_SUB_TYPE => 2, self::DATA => 3, self::FILE_SIZE => 4, self::DUPLICATION_KEY => 5, self::STATUS => 6, self::ABORT => 7, self::CHECK_AGAIN_TIMEOUT => 8, self::PROGRESS => 9, self::MESSAGE => 10, self::DESCRIPTION => 11, self::UPDATES_COUNT => 12, self::CREATED_AT => 13, self::CREATED_BY => 14, self::UPDATED_AT => 15, self::UPDATED_BY => 16, self::DELETED_AT => 17, self::PRIORITY => 18, self::WORK_GROUP_ID => 19, self::QUEUE_TIME => 20, self::FINISH_TIME => 21, self::ENTRY_ID => 22, self::PARTNER_ID => 23, self::SUBP_ID => 24, self::SCHEDULER_ID => 25, self::WORKER_ID => 26, self::BATCH_INDEX => 27, self::LAST_SCHEDULER_ID => 28, self::LAST_WORKER_ID => 29, self::LAST_WORKER_REMOTE => 30, self::PROCESSOR_EXPIRATION => 31, self::EXECUTION_ATTEMPTS => 32, self::LOCK_VERSION => 33, self::TWIN_JOB_ID => 34, self::BULK_JOB_ID => 35, self::ROOT_JOB_ID => 36, self::PARENT_JOB_ID => 37, self::DC => 38, self::ERR_TYPE => 39, self::ERR_NUMBER => 40, self::ON_STRESS_DIVERT_TO => 41, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'job_type' => 1, 'job_sub_type' => 2, 'data' => 3, 'file_size' => 4, 'duplication_key' => 5, 'status' => 6, 'abort' => 7, 'check_again_timeout' => 8, 'progress' => 9, 'message' => 10, 'description' => 11, 'updates_count' => 12, 'created_at' => 13, 'created_by' => 14, 'updated_at' => 15, 'updated_by' => 16, 'deleted_at' => 17, 'priority' => 18, 'work_group_id' => 19, 'queue_time' => 20, 'finish_time' => 21, 'entry_id' => 22, 'partner_id' => 23, 'subp_id' => 24, 'scheduler_id' => 25, 'worker_id' => 26, 'batch_index' => 27, 'last_scheduler_id' => 28, 'last_worker_id' => 29, 'last_worker_remote' => 30, 'processor_expiration' => 31, 'execution_attempts' => 32, 'lock_version' => 33, 'twin_job_id' => 34, 'bulk_job_id' => 35, 'root_job_id' => 36, 'parent_job_id' => 37, 'dc' => 38, 'err_type' => 39, 'err_number' => 40, 'on_stress_divert_to' => 41, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. BatchJobPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(BatchJobPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{
		$criteria->addSelectColumn(BatchJobPeer::ID);
		$criteria->addSelectColumn(BatchJobPeer::JOB_TYPE);
		$criteria->addSelectColumn(BatchJobPeer::JOB_SUB_TYPE);
		$criteria->addSelectColumn(BatchJobPeer::DATA);
		$criteria->addSelectColumn(BatchJobPeer::FILE_SIZE);
		$criteria->addSelectColumn(BatchJobPeer::DUPLICATION_KEY);
		$criteria->addSelectColumn(BatchJobPeer::STATUS);
		$criteria->addSelectColumn(BatchJobPeer::ABORT);
		$criteria->addSelectColumn(BatchJobPeer::CHECK_AGAIN_TIMEOUT);
		$criteria->addSelectColumn(BatchJobPeer::PROGRESS);
		$criteria->addSelectColumn(BatchJobPeer::MESSAGE);
		$criteria->addSelectColumn(BatchJobPeer::DESCRIPTION);
		$criteria->addSelectColumn(BatchJobPeer::UPDATES_COUNT);
		$criteria->addSelectColumn(BatchJobPeer::CREATED_AT);
		$criteria->addSelectColumn(BatchJobPeer::CREATED_BY);
		$criteria->addSelectColumn(BatchJobPeer::UPDATED_AT);
		$criteria->addSelectColumn(BatchJobPeer::UPDATED_BY);
		$criteria->addSelectColumn(BatchJobPeer::DELETED_AT);
		$criteria->addSelectColumn(BatchJobPeer::PRIORITY);
		$criteria->addSelectColumn(BatchJobPeer::WORK_GROUP_ID);
		$criteria->addSelectColumn(BatchJobPeer::QUEUE_TIME);
		$criteria->addSelectColumn(BatchJobPeer::FINISH_TIME);
		$criteria->addSelectColumn(BatchJobPeer::ENTRY_ID);
		$criteria->addSelectColumn(BatchJobPeer::PARTNER_ID);
		$criteria->addSelectColumn(BatchJobPeer::SUBP_ID);
		$criteria->addSelectColumn(BatchJobPeer::SCHEDULER_ID);
		$criteria->addSelectColumn(BatchJobPeer::WORKER_ID);
		$criteria->addSelectColumn(BatchJobPeer::BATCH_INDEX);
		$criteria->addSelectColumn(BatchJobPeer::LAST_SCHEDULER_ID);
		$criteria->addSelectColumn(BatchJobPeer::LAST_WORKER_ID);
		$criteria->addSelectColumn(BatchJobPeer::LAST_WORKER_REMOTE);
		$criteria->addSelectColumn(BatchJobPeer::PROCESSOR_EXPIRATION);
		$criteria->addSelectColumn(BatchJobPeer::EXECUTION_ATTEMPTS);
		$criteria->addSelectColumn(BatchJobPeer::LOCK_VERSION);
		$criteria->addSelectColumn(BatchJobPeer::TWIN_JOB_ID);
		$criteria->addSelectColumn(BatchJobPeer::BULK_JOB_ID);
		$criteria->addSelectColumn(BatchJobPeer::ROOT_JOB_ID);
		$criteria->addSelectColumn(BatchJobPeer::PARENT_JOB_ID);
		$criteria->addSelectColumn(BatchJobPeer::DC);
		$criteria->addSelectColumn(BatchJobPeer::ERR_TYPE);
		$criteria->addSelectColumn(BatchJobPeer::ERR_NUMBER);
		$criteria->addSelectColumn(BatchJobPeer::ON_STRESS_DIVERT_TO);
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(BatchJobPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			BatchJobPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		BatchJobPeer::attachCriteriaFilter($criteria);

		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'BatchJobPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// set the connection to slave server
		$con = BatchJobPeer::alternativeCon ($con);
		
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);
		
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $count);
		}
		
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     BatchJob
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = BatchJobPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	
	/**
	 * Override in order to use the query cache.
	 * Cache invalidation keys are used to determine when cached queries are valid.
	 * Before returning a query result from the cache, the time of the cached query
	 * is compared to the time saved in the invalidation key.
	 * A cached query will only be used if it's newer than the matching invalidation key.
	 *  
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      string $queryType The type of the query: select / count.
	 * @return     string The invalidation key that should be checked before returning a cached result for this criteria.
	 *		 if null is returned, the query cache won't be used - the query will be performed on the DB.
	 */
	public static function getCacheInvalidationKeys(Criteria $criteria, $queryType)
	{
		return array();
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 */
	public static function filterSelectResults(&$selectResults)
	{
	}
	
	/**
	 * Adds the supplied object array to the instance pool, objects already found in the pool
	 * will be replaced with instance from the pool.
	 *  
	 * @param      array $queryResult The array of objects to get / add to pool.
	 */
	public static function updateInstancePool(&$queryResult)
	{
		foreach ($queryResult as $curIndex => $curObject)
		{
			$objFromPool = BatchJobPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				BatchJobPeer::addInstanceToPool($curObject);
			}
			else
			{
				$queryResult[$curIndex] = $objFromPool;
			}
		}
	}
	
	/**
	 * Adds the supplied object array to the instance pool.
	 *  
	 * @param      array $queryResult The array of objects to add to pool.
	 */
	public static function addInstancesToPool($queryResult)
	{
		foreach ($queryResult as $curResult)
		{
			BatchJobPeer::addInstanceToPool($curResult);
		}
	}
	
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{		
		$criteria = BatchJobPeer::prepareCriteriaForSelect($criteria);
		
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_SELECT,
			'BatchJobPeer', 
			$cacheKey);
		if ($cachedResult !== null)
		{
			BatchJobPeer::filterSelectResults($cachedResult);
			BatchJobPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}

		$con = BatchJobPeer::alternativeCon($con);
		
		$queryResult = BatchJobPeer::populateObjects(BasePeer::doSelect($criteria, $con));
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
		}
		
		BatchJobPeer::filterSelectResults($queryResult);
		BatchJobPeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = BatchJobPeer::getCriteriaFilter();
		
		if ( $use )  $criteria_filter->enable(); 
		else $criteria_filter->disable();
	}
	
	/**
	 * Returns the default criteria filter
	 *
	 * @return     criteriaFilter The default criteria filter.
	 */
	public static function &getCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			BatchJobPeer::setDefaultCriteriaFilter();
		
		return self::$s_criteria_filter;
	}
	
	 
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new myCriteria(); 
		self::$s_criteria_filter->setFilter($c);
	}
	
	
	/**
	 * the filterCriteria will filter out all the doSelect methods - ONLY if the filter is turned on.
	 * IMPORTANT - the filter is turend on by default and when switched off - should be turned on again manually .
	 * 
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 */
	protected static function attachCriteriaFilter(Criteria $criteria)
	{
		BatchJobPeer::getCriteriaFilter()->applyFilter($criteria);
	}
	
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		
		if(!$privatePartnerData)
		{
			// the private partner data is not allowed - 
			if($kalturaNetwork)
			{
				// allow only the kaltura netword stuff
				if($partnerId)
				{
					$orderBy = "(" . self::PARTNER_ID . "<>{$partnerId})";  // first take the pattner_id and then the rest
					myCriteria::addComment($criteria , "Only Kaltura Network");
					$criteria->addAscendingOrderByColumn($orderBy);//, Criteria::CUSTOM );
				}
			}
			else
			{
				// no private data and no kaltura_network - 
				// add a criteria that will return nothing
				$criteria->addAnd(self::PARTNER_ID, Partner::PARTNER_THAT_DOWS_NOT_EXIST);
			}
		}
		else
		{
			// private data is allowed
			if(empty($partnerGroup) && empty($kalturaNetwork))
			{
				// the default case
				$criteria->addAnd(self::PARTNER_ID, $partnerId);
			}
			elseif ($partnerGroup == myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
			{
				// all is allowed - don't add anything to the criteria
			}
			else 
			{
				$criterion = null;
				if($partnerGroup)
				{
					// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
					$partners = explode(',', trim($partnerGroup));
					foreach($partners as &$p)
						trim($p); // make sure there are not leading or trailing spaces
	
					// add the partner_id to the partner_group
					if (!in_array($partnerId, $partners))
					{
						// NOTE: we need to add the partner as a string since we want all
						// the PATNER_ID IN () values to be of the same type.
						// otherwise mysql will fail choosing the right index and will
						// do a full table scan
						$partners[] = "".$partnerId;
					}
					
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
				}	
				
				$criteria->addAnd($criterion);
			}
		}
			
		$criteriaFilter->enable();
	}
	
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doCount()
	 */
	public static function doCountStmt(Criteria $criteria, PropelPDO $con = null)
	{
		// attach default criteria
		BatchJobPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = BatchJobPeer::alternativeCon ( $con );
		
		// BasePeer returns a PDOStatement
		return BasePeer::doCount($criteria, $con);
	}
	
	public static function prepareCriteriaForSelect(Criteria $criteria)
	{
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				BatchJobPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			BatchJobPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		BatchJobPeer::attachCriteriaFilter($criteria);

		return $criteria;
	}
	
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		$con = BatchJobPeer::alternativeCon($con);
		
		$criteria = BatchJobPeer::prepareCriteriaForSelect($criteria);
		
		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      BatchJob $value A BatchJob object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(BatchJob $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A BatchJob object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof BatchJob) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or BatchJob object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     BatchJob Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		foreach (self::$instances as $instance)
		{
			$instance->clearAllReferences(false);
		}
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to batch_job
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null) {
			return null;
		}
		return (string) $row[$startcol];
	}

	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = BatchJobPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = BatchJobPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = BatchJobPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseBatchJobPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseBatchJobPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new BatchJobTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean  Whether or not to return the path wit hthe class name 
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? BatchJobPeer::CLASS_DEFAULT : BatchJobPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a BatchJob or Criteria object.
	 *
	 * @param      mixed $values Criteria or BatchJob object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from BatchJob object
		}

		if ($criteria->containsKey(BatchJobPeer::ID) && $criteria->keyContainsValue(BatchJobPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.BatchJobPeer::ID.')');
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a BatchJob or Criteria object.
	 *
	 * @param      mixed $values Criteria or BatchJob object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(BatchJobPeer::ID);
			$selectCriteria->add(BatchJobPeer::ID, $criteria->remove(BatchJobPeer::ID), $comparison);

		} else { // $values is BatchJob object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the batch_job table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(BatchJobPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			BatchJobPeer::clearInstancePool();
			BatchJobPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a BatchJob or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or BatchJob object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			BatchJobPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof BatchJob) { // it's a model object
			// invalidate the cache for this single object
			BatchJobPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(BatchJobPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				BatchJobPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			BatchJobPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given BatchJob object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      BatchJob $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(BatchJob $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(BatchJobPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(BatchJobPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(BatchJobPeer::DATABASE_NAME, BatchJobPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     BatchJob
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = BatchJobPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);
		$criteria->add(BatchJobPeer::ID, $pk);

		$v = BatchJobPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(BatchJobPeer::DATABASE_NAME);
			$criteria->add(BatchJobPeer::ID, $pks, Criteria::IN);
			$objs = BatchJobPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseBatchJobPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseBatchJobPeer::buildTableMap();

