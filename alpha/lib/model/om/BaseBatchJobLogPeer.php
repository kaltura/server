<?php

/**
 * Base static class for performing query and update operations on the 'batch_job_log' table.
 *
 * 
 *
 * @package Core
 * @subpackage model.om
 */
abstract class BaseBatchJobLogPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'batch_job_log';

	/** the related Propel class for this table */
	const OM_CLASS = 'BatchJobLog';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'Core.BatchJobLog';

	/** the related TableMap class for this table */
	const TM_CLASS = 'BatchJobLogTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 49;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'batch_job_log.ID';

	/** the column name for the JOB_ID field */
	const JOB_ID = 'batch_job_log.JOB_ID';

	/** the column name for the JOB_TYPE field */
	const JOB_TYPE = 'batch_job_log.JOB_TYPE';

	/** the column name for the JOB_SUB_TYPE field */
	const JOB_SUB_TYPE = 'batch_job_log.JOB_SUB_TYPE';

	/** the column name for the DATA field */
	const DATA = 'batch_job_log.DATA';

	/** the column name for the FILE_SIZE field */
	const FILE_SIZE = 'batch_job_log.FILE_SIZE';

	/** the column name for the DUPLICATION_KEY field */
	const DUPLICATION_KEY = 'batch_job_log.DUPLICATION_KEY';

	/** the column name for the LOG_STATUS field */
	const LOG_STATUS = 'batch_job_log.LOG_STATUS';

	/** the column name for the STATUS field */
	const STATUS = 'batch_job_log.STATUS';

	/** the column name for the ABORT field */
	const ABORT = 'batch_job_log.ABORT';

	/** the column name for the CHECK_AGAIN_TIMEOUT field */
	const CHECK_AGAIN_TIMEOUT = 'batch_job_log.CHECK_AGAIN_TIMEOUT';

	/** the column name for the PROGRESS field */
	const PROGRESS = 'batch_job_log.PROGRESS';

	/** the column name for the MESSAGE field */
	const MESSAGE = 'batch_job_log.MESSAGE';

	/** the column name for the DESCRIPTION field */
	const DESCRIPTION = 'batch_job_log.DESCRIPTION';

	/** the column name for the UPDATES_COUNT field */
	const UPDATES_COUNT = 'batch_job_log.UPDATES_COUNT';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'batch_job_log.CREATED_AT';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'batch_job_log.CREATED_BY';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'batch_job_log.UPDATED_AT';

	/** the column name for the UPDATED_BY field */
	const UPDATED_BY = 'batch_job_log.UPDATED_BY';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'batch_job_log.DELETED_AT';

	/** the column name for the PRIORITY field */
	const PRIORITY = 'batch_job_log.PRIORITY';

	/** the column name for the WORK_GROUP_ID field */
	const WORK_GROUP_ID = 'batch_job_log.WORK_GROUP_ID';

	/** the column name for the QUEUE_TIME field */
	const QUEUE_TIME = 'batch_job_log.QUEUE_TIME';

	/** the column name for the FINISH_TIME field */
	const FINISH_TIME = 'batch_job_log.FINISH_TIME';

	/** the column name for the ENTRY_ID field */
	const ENTRY_ID = 'batch_job_log.ENTRY_ID';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'batch_job_log.PARTNER_ID';

	/** the column name for the SUBP_ID field */
	const SUBP_ID = 'batch_job_log.SUBP_ID';

	/** the column name for the SCHEDULER_ID field */
	const SCHEDULER_ID = 'batch_job_log.SCHEDULER_ID';

	/** the column name for the WORKER_ID field */
	const WORKER_ID = 'batch_job_log.WORKER_ID';

	/** the column name for the BATCH_INDEX field */
	const BATCH_INDEX = 'batch_job_log.BATCH_INDEX';

	/** the column name for the LAST_SCHEDULER_ID field */
	const LAST_SCHEDULER_ID = 'batch_job_log.LAST_SCHEDULER_ID';

	/** the column name for the LAST_WORKER_ID field */
	const LAST_WORKER_ID = 'batch_job_log.LAST_WORKER_ID';

	/** the column name for the LAST_WORKER_REMOTE field */
	const LAST_WORKER_REMOTE = 'batch_job_log.LAST_WORKER_REMOTE';

	/** the column name for the PROCESSOR_EXPIRATION field */
	const PROCESSOR_EXPIRATION = 'batch_job_log.PROCESSOR_EXPIRATION';

	/** the column name for the EXECUTION_ATTEMPTS field */
	const EXECUTION_ATTEMPTS = 'batch_job_log.EXECUTION_ATTEMPTS';

	/** the column name for the LOCK_VERSION field */
	const LOCK_VERSION = 'batch_job_log.LOCK_VERSION';

	/** the column name for the TWIN_JOB_ID field */
	const TWIN_JOB_ID = 'batch_job_log.TWIN_JOB_ID';

	/** the column name for the BULK_JOB_ID field */
	const BULK_JOB_ID = 'batch_job_log.BULK_JOB_ID';

	/** the column name for the ROOT_JOB_ID field */
	const ROOT_JOB_ID = 'batch_job_log.ROOT_JOB_ID';

	/** the column name for the PARENT_JOB_ID field */
	const PARENT_JOB_ID = 'batch_job_log.PARENT_JOB_ID';

	/** the column name for the DC field */
	const DC = 'batch_job_log.DC';

	/** the column name for the ERR_TYPE field */
	const ERR_TYPE = 'batch_job_log.ERR_TYPE';

	/** the column name for the ERR_NUMBER field */
	const ERR_NUMBER = 'batch_job_log.ERR_NUMBER';

	/** the column name for the ON_STRESS_DIVERT_TO field */
	const ON_STRESS_DIVERT_TO = 'batch_job_log.ON_STRESS_DIVERT_TO';

	/** the column name for the PARAM_1 field */
	const PARAM_1 = 'batch_job_log.PARAM_1';

	/** the column name for the PARAM_2 field */
	const PARAM_2 = 'batch_job_log.PARAM_2';

	/** the column name for the PARAM_3 field */
	const PARAM_3 = 'batch_job_log.PARAM_3';

	/** the column name for the PARAM_4 field */
	const PARAM_4 = 'batch_job_log.PARAM_4';

	/** the column name for the PARAM_5 field */
	const PARAM_5 = 'batch_job_log.PARAM_5';

	/**
	 * An identiy map to hold any loaded instances of BatchJobLog objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array BatchJobLog[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'JobId', 'JobType', 'JobSubType', 'Data', 'FileSize', 'DuplicationKey', 'LogStatus', 'Status', 'Abort', 'CheckAgainTimeout', 'Progress', 'Message', 'Description', 'UpdatesCount', 'CreatedAt', 'CreatedBy', 'UpdatedAt', 'UpdatedBy', 'DeletedAt', 'Priority', 'WorkGroupId', 'QueueTime', 'FinishTime', 'EntryId', 'PartnerId', 'SubpId', 'SchedulerId', 'WorkerId', 'BatchIndex', 'LastSchedulerId', 'LastWorkerId', 'LastWorkerRemote', 'ProcessorExpiration', 'ExecutionAttempts', 'LockVersion', 'TwinJobId', 'BulkJobId', 'RootJobId', 'ParentJobId', 'Dc', 'ErrType', 'ErrNumber', 'OnStressDivertTo', 'Param1', 'Param2', 'Param3', 'Param4', 'Param5', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'jobId', 'jobType', 'jobSubType', 'data', 'fileSize', 'duplicationKey', 'logStatus', 'status', 'abort', 'checkAgainTimeout', 'progress', 'message', 'description', 'updatesCount', 'createdAt', 'createdBy', 'updatedAt', 'updatedBy', 'deletedAt', 'priority', 'workGroupId', 'queueTime', 'finishTime', 'entryId', 'partnerId', 'subpId', 'schedulerId', 'workerId', 'batchIndex', 'lastSchedulerId', 'lastWorkerId', 'lastWorkerRemote', 'processorExpiration', 'executionAttempts', 'lockVersion', 'twinJobId', 'bulkJobId', 'rootJobId', 'parentJobId', 'dc', 'errType', 'errNumber', 'onStressDivertTo', 'param1', 'param2', 'param3', 'param4', 'param5', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::JOB_ID, self::JOB_TYPE, self::JOB_SUB_TYPE, self::DATA, self::FILE_SIZE, self::DUPLICATION_KEY, self::LOG_STATUS, self::STATUS, self::ABORT, self::CHECK_AGAIN_TIMEOUT, self::PROGRESS, self::MESSAGE, self::DESCRIPTION, self::UPDATES_COUNT, self::CREATED_AT, self::CREATED_BY, self::UPDATED_AT, self::UPDATED_BY, self::DELETED_AT, self::PRIORITY, self::WORK_GROUP_ID, self::QUEUE_TIME, self::FINISH_TIME, self::ENTRY_ID, self::PARTNER_ID, self::SUBP_ID, self::SCHEDULER_ID, self::WORKER_ID, self::BATCH_INDEX, self::LAST_SCHEDULER_ID, self::LAST_WORKER_ID, self::LAST_WORKER_REMOTE, self::PROCESSOR_EXPIRATION, self::EXECUTION_ATTEMPTS, self::LOCK_VERSION, self::TWIN_JOB_ID, self::BULK_JOB_ID, self::ROOT_JOB_ID, self::PARENT_JOB_ID, self::DC, self::ERR_TYPE, self::ERR_NUMBER, self::ON_STRESS_DIVERT_TO, self::PARAM_1, self::PARAM_2, self::PARAM_3, self::PARAM_4, self::PARAM_5, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'job_id', 'job_type', 'job_sub_type', 'data', 'file_size', 'duplication_key', 'log_status', 'status', 'abort', 'check_again_timeout', 'progress', 'message', 'description', 'updates_count', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'priority', 'work_group_id', 'queue_time', 'finish_time', 'entry_id', 'partner_id', 'subp_id', 'scheduler_id', 'worker_id', 'batch_index', 'last_scheduler_id', 'last_worker_id', 'last_worker_remote', 'processor_expiration', 'execution_attempts', 'lock_version', 'twin_job_id', 'bulk_job_id', 'root_job_id', 'parent_job_id', 'dc', 'err_type', 'err_number', 'on_stress_divert_to', 'param_1', 'param_2', 'param_3', 'param_4', 'param_5', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'JobId' => 1, 'JobType' => 2, 'JobSubType' => 3, 'Data' => 4, 'FileSize' => 5, 'DuplicationKey' => 6, 'LogStatus' => 7, 'Status' => 8, 'Abort' => 9, 'CheckAgainTimeout' => 10, 'Progress' => 11, 'Message' => 12, 'Description' => 13, 'UpdatesCount' => 14, 'CreatedAt' => 15, 'CreatedBy' => 16, 'UpdatedAt' => 17, 'UpdatedBy' => 18, 'DeletedAt' => 19, 'Priority' => 20, 'WorkGroupId' => 21, 'QueueTime' => 22, 'FinishTime' => 23, 'EntryId' => 24, 'PartnerId' => 25, 'SubpId' => 26, 'SchedulerId' => 27, 'WorkerId' => 28, 'BatchIndex' => 29, 'LastSchedulerId' => 30, 'LastWorkerId' => 31, 'LastWorkerRemote' => 32, 'ProcessorExpiration' => 33, 'ExecutionAttempts' => 34, 'LockVersion' => 35, 'TwinJobId' => 36, 'BulkJobId' => 37, 'RootJobId' => 38, 'ParentJobId' => 39, 'Dc' => 40, 'ErrType' => 41, 'ErrNumber' => 42, 'OnStressDivertTo' => 43, 'Param1' => 44, 'Param2' => 45, 'Param3' => 46, 'Param4' => 47, 'Param5' => 48, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'jobId' => 1, 'jobType' => 2, 'jobSubType' => 3, 'data' => 4, 'fileSize' => 5, 'duplicationKey' => 6, 'logStatus' => 7, 'status' => 8, 'abort' => 9, 'checkAgainTimeout' => 10, 'progress' => 11, 'message' => 12, 'description' => 13, 'updatesCount' => 14, 'createdAt' => 15, 'createdBy' => 16, 'updatedAt' => 17, 'updatedBy' => 18, 'deletedAt' => 19, 'priority' => 20, 'workGroupId' => 21, 'queueTime' => 22, 'finishTime' => 23, 'entryId' => 24, 'partnerId' => 25, 'subpId' => 26, 'schedulerId' => 27, 'workerId' => 28, 'batchIndex' => 29, 'lastSchedulerId' => 30, 'lastWorkerId' => 31, 'lastWorkerRemote' => 32, 'processorExpiration' => 33, 'executionAttempts' => 34, 'lockVersion' => 35, 'twinJobId' => 36, 'bulkJobId' => 37, 'rootJobId' => 38, 'parentJobId' => 39, 'dc' => 40, 'errType' => 41, 'errNumber' => 42, 'onStressDivertTo' => 43, 'param1' => 44, 'param2' => 45, 'param3' => 46, 'param4' => 47, 'param5' => 48, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::JOB_ID => 1, self::JOB_TYPE => 2, self::JOB_SUB_TYPE => 3, self::DATA => 4, self::FILE_SIZE => 5, self::DUPLICATION_KEY => 6, self::LOG_STATUS => 7, self::STATUS => 8, self::ABORT => 9, self::CHECK_AGAIN_TIMEOUT => 10, self::PROGRESS => 11, self::MESSAGE => 12, self::DESCRIPTION => 13, self::UPDATES_COUNT => 14, self::CREATED_AT => 15, self::CREATED_BY => 16, self::UPDATED_AT => 17, self::UPDATED_BY => 18, self::DELETED_AT => 19, self::PRIORITY => 20, self::WORK_GROUP_ID => 21, self::QUEUE_TIME => 22, self::FINISH_TIME => 23, self::ENTRY_ID => 24, self::PARTNER_ID => 25, self::SUBP_ID => 26, self::SCHEDULER_ID => 27, self::WORKER_ID => 28, self::BATCH_INDEX => 29, self::LAST_SCHEDULER_ID => 30, self::LAST_WORKER_ID => 31, self::LAST_WORKER_REMOTE => 32, self::PROCESSOR_EXPIRATION => 33, self::EXECUTION_ATTEMPTS => 34, self::LOCK_VERSION => 35, self::TWIN_JOB_ID => 36, self::BULK_JOB_ID => 37, self::ROOT_JOB_ID => 38, self::PARENT_JOB_ID => 39, self::DC => 40, self::ERR_TYPE => 41, self::ERR_NUMBER => 42, self::ON_STRESS_DIVERT_TO => 43, self::PARAM_1 => 44, self::PARAM_2 => 45, self::PARAM_3 => 46, self::PARAM_4 => 47, self::PARAM_5 => 48, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'job_id' => 1, 'job_type' => 2, 'job_sub_type' => 3, 'data' => 4, 'file_size' => 5, 'duplication_key' => 6, 'log_status' => 7, 'status' => 8, 'abort' => 9, 'check_again_timeout' => 10, 'progress' => 11, 'message' => 12, 'description' => 13, 'updates_count' => 14, 'created_at' => 15, 'created_by' => 16, 'updated_at' => 17, 'updated_by' => 18, 'deleted_at' => 19, 'priority' => 20, 'work_group_id' => 21, 'queue_time' => 22, 'finish_time' => 23, 'entry_id' => 24, 'partner_id' => 25, 'subp_id' => 26, 'scheduler_id' => 27, 'worker_id' => 28, 'batch_index' => 29, 'last_scheduler_id' => 30, 'last_worker_id' => 31, 'last_worker_remote' => 32, 'processor_expiration' => 33, 'execution_attempts' => 34, 'lock_version' => 35, 'twin_job_id' => 36, 'bulk_job_id' => 37, 'root_job_id' => 38, 'parent_job_id' => 39, 'dc' => 40, 'err_type' => 41, 'err_number' => 42, 'on_stress_divert_to' => 43, 'param_1' => 44, 'param_2' => 45, 'param_3' => 46, 'param_4' => 47, 'param_5' => 48, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, )
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
	 * @param      string $column The column name for current table. (i.e. BatchJobLogPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(BatchJobLogPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(BatchJobLogPeer::ID);
		$criteria->addSelectColumn(BatchJobLogPeer::JOB_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::JOB_TYPE);
		$criteria->addSelectColumn(BatchJobLogPeer::JOB_SUB_TYPE);
		$criteria->addSelectColumn(BatchJobLogPeer::DATA);
		$criteria->addSelectColumn(BatchJobLogPeer::FILE_SIZE);
		$criteria->addSelectColumn(BatchJobLogPeer::DUPLICATION_KEY);
		$criteria->addSelectColumn(BatchJobLogPeer::LOG_STATUS);
		$criteria->addSelectColumn(BatchJobLogPeer::STATUS);
		$criteria->addSelectColumn(BatchJobLogPeer::ABORT);
		$criteria->addSelectColumn(BatchJobLogPeer::CHECK_AGAIN_TIMEOUT);
		$criteria->addSelectColumn(BatchJobLogPeer::PROGRESS);
		$criteria->addSelectColumn(BatchJobLogPeer::MESSAGE);
		$criteria->addSelectColumn(BatchJobLogPeer::DESCRIPTION);
		$criteria->addSelectColumn(BatchJobLogPeer::UPDATES_COUNT);
		$criteria->addSelectColumn(BatchJobLogPeer::CREATED_AT);
		$criteria->addSelectColumn(BatchJobLogPeer::CREATED_BY);
		$criteria->addSelectColumn(BatchJobLogPeer::UPDATED_AT);
		$criteria->addSelectColumn(BatchJobLogPeer::UPDATED_BY);
		$criteria->addSelectColumn(BatchJobLogPeer::DELETED_AT);
		$criteria->addSelectColumn(BatchJobLogPeer::PRIORITY);
		$criteria->addSelectColumn(BatchJobLogPeer::WORK_GROUP_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::QUEUE_TIME);
		$criteria->addSelectColumn(BatchJobLogPeer::FINISH_TIME);
		$criteria->addSelectColumn(BatchJobLogPeer::ENTRY_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::PARTNER_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::SUBP_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::SCHEDULER_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::WORKER_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::BATCH_INDEX);
		$criteria->addSelectColumn(BatchJobLogPeer::LAST_SCHEDULER_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::LAST_WORKER_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::LAST_WORKER_REMOTE);
		$criteria->addSelectColumn(BatchJobLogPeer::PROCESSOR_EXPIRATION);
		$criteria->addSelectColumn(BatchJobLogPeer::EXECUTION_ATTEMPTS);
		$criteria->addSelectColumn(BatchJobLogPeer::LOCK_VERSION);
		$criteria->addSelectColumn(BatchJobLogPeer::TWIN_JOB_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::BULK_JOB_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::ROOT_JOB_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::PARENT_JOB_ID);
		$criteria->addSelectColumn(BatchJobLogPeer::DC);
		$criteria->addSelectColumn(BatchJobLogPeer::ERR_TYPE);
		$criteria->addSelectColumn(BatchJobLogPeer::ERR_NUMBER);
		$criteria->addSelectColumn(BatchJobLogPeer::ON_STRESS_DIVERT_TO);
		$criteria->addSelectColumn(BatchJobLogPeer::PARAM_1);
		$criteria->addSelectColumn(BatchJobLogPeer::PARAM_2);
		$criteria->addSelectColumn(BatchJobLogPeer::PARAM_3);
		$criteria->addSelectColumn(BatchJobLogPeer::PARAM_4);
		$criteria->addSelectColumn(BatchJobLogPeer::PARAM_5);
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
		$criteria->setPrimaryTableName(BatchJobLogPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			BatchJobLogPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		BatchJobLogPeer::attachCriteriaFilter($criteria);

		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteria, 
			kQueryCache::QUERY_TYPE_COUNT,
			'BatchJobLogPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			return $cachedResult;
		}
		
		// select the connection for the query
		$con = BatchJobLogPeer::alternativeCon ($con, $queryDB);
		
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
	 * @return     BatchJobLog
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = BatchJobLogPeer::doSelect($critcopy, $con);
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
	 * @return     array The invalidation keys that should be checked before returning a cached result for this criteria.
	 *		 if an empty array is returned, the query cache won't be used - the query will be performed on the DB.
	 */
	public static function getCacheInvalidationKeys()
	{
		return array();
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 * @param	   Criteria $criteria
	 */
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
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
			$objFromPool = BatchJobLogPeer::getInstanceFromPool($curObject->getPrimaryKey());
			if ($objFromPool === null)
			{
				BatchJobLogPeer::addInstanceToPool($curObject);
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
		if (Propel::isInstancePoolingEnabled())
		{
			if ( count( self::$instances ) + count( $queryResult ) <= kConf::get('max_num_instances_in_pool') )
			{  
				foreach ($queryResult as $curResult)
				{
					BatchJobLogPeer::addInstanceToPool($curResult);
				}
			}
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
		$criteriaForSelect = BatchJobLogPeer::prepareCriteriaForSelect($criteria);
		
		$queryDB = kQueryCache::QUERY_DB_UNDEFINED;
		$cacheKey = null;
		$cachedResult = kQueryCache::getCachedQueryResults(
			$criteriaForSelect, 
			kQueryCache::QUERY_TYPE_SELECT,
			'BatchJobLogPeer', 
			$cacheKey, 
			$queryDB);
		if ($cachedResult !== null)
		{
			$cacheKey = null;
			BatchJobLogPeer::filterSelectResults($cachedResult, $criteriaForSelect);
			BatchJobLogPeer::updateInstancePool($cachedResult);
			return $cachedResult;
		}
		
		$con = BatchJobLogPeer::alternativeCon($con, $queryDB);
		
		$queryResult = BatchJobLogPeer::populateObjects(BasePeer::doSelect($criteriaForSelect, $con));
		
		if($criteriaForSelect instanceof KalturaCriteria)
			$criteriaForSelect->applyResultsSort($queryResult);
		
		if ($cacheKey !== null)
		{
			kQueryCache::cacheQueryResults($cacheKey, $queryResult);
			$cacheKey = null;
		}
		
		BatchJobLogPeer::filterSelectResults($queryResult, $criteria);
		
		BatchJobLogPeer::addInstancesToPool($queryResult);
		return $queryResult;
	}

	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		if ($con === null)
		{
			switch ($queryDB)
			{
			case kQueryCache::QUERY_DB_MASTER:
				$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
				break;

			case kQueryCache::QUERY_DB_SLAVE:
				$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
				break;
			}
		}
	
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = BatchJobLogPeer::getCriteriaFilter();
		
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
			BatchJobLogPeer::setDefaultCriteriaFilter();
		
		$partnerCriteria = myPartnerUtils::getPartnerCriteriaParams('BatchJobLog');
		if ($partnerCriteria)
		{
			call_user_func_array(array('BatchJobLogPeer','addPartnerToCriteria'), $partnerCriteria);
		}
		
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
		BatchJobLogPeer::getCriteriaFilter()->applyFilter($criteria);
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
			if(!strlen(strval($partnerGroup)))
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
				// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
				$partners = explode(',', trim($partnerGroup));
				foreach($partners as &$p)
					trim($p); // make sure there are not leading or trailing spaces

				// add the partner_id to the partner_group
				if (!in_array(strval($partnerId), $partners))
					$partners[] = strval($partnerId);
				
				if(count($partners) == 1 && reset($partners) == $partnerId)
				{
					$criteria->addAnd(self::PARTNER_ID, $partnerId);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
					$criteria->addAnd($criterion);
				}
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
		BatchJobLogPeer::attachCriteriaFilter($criteria);
		
		// select the connection for the query
		$con = BatchJobLogPeer::alternativeCon ( $con );
		
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
				BatchJobLogPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			BatchJobLogPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		BatchJobLogPeer::attachCriteriaFilter($criteria);

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
		$con = BatchJobLogPeer::alternativeCon($con);
		
		$criteria = BatchJobLogPeer::prepareCriteriaForSelect($criteria);
		
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
	 * @param      BatchJobLog $value A BatchJobLog object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(BatchJobLog $obj, $key = null)
	{
		if ( Propel::isInstancePoolingEnabled() )
		{
			if ( $key === null )
			{
				$key = (string) $obj->getId();
			}
				
			if ( isset( self::$instances[$key] )											// Instance is already mapped?
					|| count( self::$instances ) < kConf::get('max_num_instances_in_pool')	// Not mapped, but max. inst. not yet reached?
				)
			{
				self::$instances[$key] = $obj;
				kMemoryManager::registerPeer('BatchJobLogPeer');
			}
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
	 * @param      mixed $value A BatchJobLog object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof BatchJobLog) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or BatchJobLog object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     BatchJobLog Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to batch_job_log
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
		$cls = BatchJobLogPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = BatchJobLogPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = BatchJobLogPeer::getInstanceFromPool($key))) {
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
	  $dbMap = Propel::getDatabaseMap(BaseBatchJobLogPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseBatchJobLogPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new BatchJobLogTableMap());
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
		return $withPrefix ? BatchJobLogPeer::CLASS_DEFAULT : BatchJobLogPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a BatchJobLog or Criteria object.
	 *
	 * @param      mixed $values Criteria or BatchJobLog object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from BatchJobLog object
		}

		if ($criteria->containsKey(BatchJobLogPeer::ID) && $criteria->keyContainsValue(BatchJobLogPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.BatchJobLogPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a BatchJobLog or Criteria object.
	 *
	 * @param      mixed $values Criteria or BatchJobLog object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(BatchJobLogPeer::ID);
			$selectCriteria->add(BatchJobLogPeer::ID, $criteria->remove(BatchJobLogPeer::ID), $comparison);

		} else { // $values is BatchJobLog object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}
	
	/**
	 * Return array of columns that should change only if there is a real change.
	 * @return array
	 */
	public static function getAtomicColumns()
	{
		return array();
	}

	/**
	 * Method to DELETE all rows from the batch_job_log table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(BatchJobLogPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			BatchJobLogPeer::clearInstancePool();
			BatchJobLogPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a BatchJobLog or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or BatchJobLog object or primary key or array of primary keys
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
			$con = Propel::getConnection(BatchJobLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			BatchJobLogPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof BatchJobLog) { // it's a model object
			// invalidate the cache for this single object
			BatchJobLogPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(BatchJobLogPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				BatchJobLogPeer::removeInstanceFromPool($singleval);
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
			BatchJobLogPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given BatchJobLog object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      BatchJobLog $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(BatchJobLog $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(BatchJobLogPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(BatchJobLogPeer::TABLE_NAME);

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

		return BasePeer::doValidate(BatchJobLogPeer::DATABASE_NAME, BatchJobLogPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     BatchJobLog
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = BatchJobLogPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(BatchJobLogPeer::DATABASE_NAME);
		$criteria->add(BatchJobLogPeer::ID, $pk);

		$v = BatchJobLogPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(BatchJobLogPeer::DATABASE_NAME);
			$criteria->add(BatchJobLogPeer::ID, $pks, Criteria::IN);
			$objs = BatchJobLogPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseBatchJobLogPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseBatchJobLogPeer::buildTableMap();

