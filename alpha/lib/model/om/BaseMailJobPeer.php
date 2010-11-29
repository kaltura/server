<?php

/**
 * Base static class for performing query and update operations on the 'mail_job' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class BaseMailJobPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'propel';

	/** the table name for this class */
	const TABLE_NAME = 'mail_job';

	/** the related Propel class for this table */
	const OM_CLASS = 'MailJob';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.model.MailJob';

	/** the related TableMap class for this table */
	const TM_CLASS = 'MailJobTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 25;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'mail_job.ID';

	/** the column name for the MAIL_TYPE field */
	const MAIL_TYPE = 'mail_job.MAIL_TYPE';

	/** the column name for the MAIL_PRIORITY field */
	const MAIL_PRIORITY = 'mail_job.MAIL_PRIORITY';

	/** the column name for the RECIPIENT_NAME field */
	const RECIPIENT_NAME = 'mail_job.RECIPIENT_NAME';

	/** the column name for the RECIPIENT_EMAIL field */
	const RECIPIENT_EMAIL = 'mail_job.RECIPIENT_EMAIL';

	/** the column name for the RECIPIENT_ID field */
	const RECIPIENT_ID = 'mail_job.RECIPIENT_ID';

	/** the column name for the FROM_NAME field */
	const FROM_NAME = 'mail_job.FROM_NAME';

	/** the column name for the FROM_EMAIL field */
	const FROM_EMAIL = 'mail_job.FROM_EMAIL';

	/** the column name for the BODY_PARAMS field */
	const BODY_PARAMS = 'mail_job.BODY_PARAMS';

	/** the column name for the SUBJECT_PARAMS field */
	const SUBJECT_PARAMS = 'mail_job.SUBJECT_PARAMS';

	/** the column name for the TEMPLATE_PATH field */
	const TEMPLATE_PATH = 'mail_job.TEMPLATE_PATH';

	/** the column name for the CULTURE field */
	const CULTURE = 'mail_job.CULTURE';

	/** the column name for the STATUS field */
	const STATUS = 'mail_job.STATUS';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'mail_job.CREATED_AT';

	/** the column name for the CAMPAIGN_ID field */
	const CAMPAIGN_ID = 'mail_job.CAMPAIGN_ID';

	/** the column name for the MIN_SEND_DATE field */
	const MIN_SEND_DATE = 'mail_job.MIN_SEND_DATE';

	/** the column name for the SCHEDULER_ID field */
	const SCHEDULER_ID = 'mail_job.SCHEDULER_ID';

	/** the column name for the WORKER_ID field */
	const WORKER_ID = 'mail_job.WORKER_ID';

	/** the column name for the BATCH_INDEX field */
	const BATCH_INDEX = 'mail_job.BATCH_INDEX';

	/** the column name for the PROCESSOR_EXPIRATION field */
	const PROCESSOR_EXPIRATION = 'mail_job.PROCESSOR_EXPIRATION';

	/** the column name for the EXECUTION_ATTEMPTS field */
	const EXECUTION_ATTEMPTS = 'mail_job.EXECUTION_ATTEMPTS';

	/** the column name for the LOCK_VERSION field */
	const LOCK_VERSION = 'mail_job.LOCK_VERSION';

	/** the column name for the PARTNER_ID field */
	const PARTNER_ID = 'mail_job.PARTNER_ID';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'mail_job.UPDATED_AT';

	/** the column name for the DC field */
	const DC = 'mail_job.DC';

	/**
	 * An identiy map to hold any loaded instances of MailJob objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array MailJob[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'MailType', 'MailPriority', 'RecipientName', 'RecipientEmail', 'RecipientId', 'FromName', 'FromEmail', 'BodyParams', 'SubjectParams', 'TemplatePath', 'Culture', 'Status', 'CreatedAt', 'CampaignId', 'MinSendDate', 'SchedulerId', 'WorkerId', 'BatchIndex', 'ProcessorExpiration', 'ExecutionAttempts', 'LockVersion', 'PartnerId', 'UpdatedAt', 'Dc', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'mailType', 'mailPriority', 'recipientName', 'recipientEmail', 'recipientId', 'fromName', 'fromEmail', 'bodyParams', 'subjectParams', 'templatePath', 'culture', 'status', 'createdAt', 'campaignId', 'minSendDate', 'schedulerId', 'workerId', 'batchIndex', 'processorExpiration', 'executionAttempts', 'lockVersion', 'partnerId', 'updatedAt', 'dc', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::MAIL_TYPE, self::MAIL_PRIORITY, self::RECIPIENT_NAME, self::RECIPIENT_EMAIL, self::RECIPIENT_ID, self::FROM_NAME, self::FROM_EMAIL, self::BODY_PARAMS, self::SUBJECT_PARAMS, self::TEMPLATE_PATH, self::CULTURE, self::STATUS, self::CREATED_AT, self::CAMPAIGN_ID, self::MIN_SEND_DATE, self::SCHEDULER_ID, self::WORKER_ID, self::BATCH_INDEX, self::PROCESSOR_EXPIRATION, self::EXECUTION_ATTEMPTS, self::LOCK_VERSION, self::PARTNER_ID, self::UPDATED_AT, self::DC, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'mail_type', 'mail_priority', 'recipient_name', 'recipient_email', 'recipient_id', 'from_name', 'from_email', 'body_params', 'subject_params', 'template_path', 'culture', 'status', 'created_at', 'campaign_id', 'min_send_date', 'scheduler_id', 'worker_id', 'batch_index', 'processor_expiration', 'execution_attempts', 'lock_version', 'partner_id', 'updated_at', 'dc', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'MailType' => 1, 'MailPriority' => 2, 'RecipientName' => 3, 'RecipientEmail' => 4, 'RecipientId' => 5, 'FromName' => 6, 'FromEmail' => 7, 'BodyParams' => 8, 'SubjectParams' => 9, 'TemplatePath' => 10, 'Culture' => 11, 'Status' => 12, 'CreatedAt' => 13, 'CampaignId' => 14, 'MinSendDate' => 15, 'SchedulerId' => 16, 'WorkerId' => 17, 'BatchIndex' => 18, 'ProcessorExpiration' => 19, 'ExecutionAttempts' => 20, 'LockVersion' => 21, 'PartnerId' => 22, 'UpdatedAt' => 23, 'Dc' => 24, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'mailType' => 1, 'mailPriority' => 2, 'recipientName' => 3, 'recipientEmail' => 4, 'recipientId' => 5, 'fromName' => 6, 'fromEmail' => 7, 'bodyParams' => 8, 'subjectParams' => 9, 'templatePath' => 10, 'culture' => 11, 'status' => 12, 'createdAt' => 13, 'campaignId' => 14, 'minSendDate' => 15, 'schedulerId' => 16, 'workerId' => 17, 'batchIndex' => 18, 'processorExpiration' => 19, 'executionAttempts' => 20, 'lockVersion' => 21, 'partnerId' => 22, 'updatedAt' => 23, 'dc' => 24, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::MAIL_TYPE => 1, self::MAIL_PRIORITY => 2, self::RECIPIENT_NAME => 3, self::RECIPIENT_EMAIL => 4, self::RECIPIENT_ID => 5, self::FROM_NAME => 6, self::FROM_EMAIL => 7, self::BODY_PARAMS => 8, self::SUBJECT_PARAMS => 9, self::TEMPLATE_PATH => 10, self::CULTURE => 11, self::STATUS => 12, self::CREATED_AT => 13, self::CAMPAIGN_ID => 14, self::MIN_SEND_DATE => 15, self::SCHEDULER_ID => 16, self::WORKER_ID => 17, self::BATCH_INDEX => 18, self::PROCESSOR_EXPIRATION => 19, self::EXECUTION_ATTEMPTS => 20, self::LOCK_VERSION => 21, self::PARTNER_ID => 22, self::UPDATED_AT => 23, self::DC => 24, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'mail_type' => 1, 'mail_priority' => 2, 'recipient_name' => 3, 'recipient_email' => 4, 'recipient_id' => 5, 'from_name' => 6, 'from_email' => 7, 'body_params' => 8, 'subject_params' => 9, 'template_path' => 10, 'culture' => 11, 'status' => 12, 'created_at' => 13, 'campaign_id' => 14, 'min_send_date' => 15, 'scheduler_id' => 16, 'worker_id' => 17, 'batch_index' => 18, 'processor_expiration' => 19, 'execution_attempts' => 20, 'lock_version' => 21, 'partner_id' => 22, 'updated_at' => 23, 'dc' => 24, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, )
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
	 * @param      string $column The column name for current table. (i.e. MailJobPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(MailJobPeer::TABLE_NAME.'.', $alias.'.', $column);
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
		$criteria->addSelectColumn(MailJobPeer::ID);
		$criteria->addSelectColumn(MailJobPeer::MAIL_TYPE);
		$criteria->addSelectColumn(MailJobPeer::MAIL_PRIORITY);
		$criteria->addSelectColumn(MailJobPeer::RECIPIENT_NAME);
		$criteria->addSelectColumn(MailJobPeer::RECIPIENT_EMAIL);
		$criteria->addSelectColumn(MailJobPeer::RECIPIENT_ID);
		$criteria->addSelectColumn(MailJobPeer::FROM_NAME);
		$criteria->addSelectColumn(MailJobPeer::FROM_EMAIL);
		$criteria->addSelectColumn(MailJobPeer::BODY_PARAMS);
		$criteria->addSelectColumn(MailJobPeer::SUBJECT_PARAMS);
		$criteria->addSelectColumn(MailJobPeer::TEMPLATE_PATH);
		$criteria->addSelectColumn(MailJobPeer::CULTURE);
		$criteria->addSelectColumn(MailJobPeer::STATUS);
		$criteria->addSelectColumn(MailJobPeer::CREATED_AT);
		$criteria->addSelectColumn(MailJobPeer::CAMPAIGN_ID);
		$criteria->addSelectColumn(MailJobPeer::MIN_SEND_DATE);
		$criteria->addSelectColumn(MailJobPeer::SCHEDULER_ID);
		$criteria->addSelectColumn(MailJobPeer::WORKER_ID);
		$criteria->addSelectColumn(MailJobPeer::BATCH_INDEX);
		$criteria->addSelectColumn(MailJobPeer::PROCESSOR_EXPIRATION);
		$criteria->addSelectColumn(MailJobPeer::EXECUTION_ATTEMPTS);
		$criteria->addSelectColumn(MailJobPeer::LOCK_VERSION);
		$criteria->addSelectColumn(MailJobPeer::PARTNER_ID);
		$criteria->addSelectColumn(MailJobPeer::UPDATED_AT);
		$criteria->addSelectColumn(MailJobPeer::DC);
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
		$criteria->setPrimaryTableName(MailJobPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			MailJobPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName
		
		// BasePeer returns a PDOStatement
		$stmt = MailJobPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     MailJob
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = MailJobPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
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
		return MailJobPeer::populateObjects(MailJobPeer::doSelectStmt($criteria, $con));
	}

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		
		return $con;
	}
		
	/**
	 * @var criteriaFilter The default criteria filter.
	 */
	protected static $s_criteria_filter;
	
	public static function  setUseCriteriaFilter ( $use )
	{
		$criteria_filter = MailJobPeer::getCriteriaFilter();
		
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
			MailJobPeer::setDefaultCriteriaFilter();
		
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
		MailJobPeer::getCriteriaFilter()->applyFilter($criteria);
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
					$partners[] = $partnerId;
					
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
		MailJobPeer::attachCriteriaFilter($criteria);
		
		// set the connection to slave server
		$con = MailJobPeer::alternativeCon ( $con );
		
		// BasePeer returns a PDOStatement
		return BasePeer::doCount($criteria, $con);
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
		$con = MailJobPeer::alternativeCon($con);
		
		if ($criteria->hasSelectClause()) 
		{
			$asColumns = $criteria->getAsColumns();
			if(count($asColumns) == 1 && isset($asColumns['_score']))
			{
				$criteria = clone $criteria;
				MailJobPeer::addSelectColumns($criteria);
			}
		}
		else
		{
			$criteria = clone $criteria;
			MailJobPeer::addSelectColumns($criteria);
		}
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// attach default criteria
		MailJobPeer::attachCriteriaFilter($criteria);
		
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
	 * @param      MailJob $value A MailJob object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(MailJob $obj, $key = null)
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
	 * @param      mixed $value A MailJob object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof MailJob) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or MailJob object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     MailJob Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to mail_job
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
		$cls = MailJobPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = MailJobPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = MailJobPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				MailJobPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related kuser table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinkuser(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(MailJobPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			MailJobPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(MailJobPeer::RECIPIENT_ID, kuserPeer::ID, $join_behavior);

		$stmt = MailJobPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of MailJob objects pre-filled with their kuser objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of MailJob objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinkuser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		MailJobPeer::addSelectColumns($criteria);
		$startcol = (MailJobPeer::NUM_COLUMNS - MailJobPeer::NUM_LAZY_LOAD_COLUMNS);
		kuserPeer::addSelectColumns($criteria);

		$criteria->addJoin(MailJobPeer::RECIPIENT_ID, kuserPeer::ID, $join_behavior);

		$stmt = MailJobPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = MailJobPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = MailJobPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = MailJobPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				MailJobPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = kuserPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kuserPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					kuserPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded
				
				// Add the $obj1 (MailJob) to $obj2 (kuser)
				$obj2->addMailJob($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(MailJobPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			MailJobPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);
		
		
		$criteria->addJoin(MailJobPeer::RECIPIENT_ID, kuserPeer::ID, $join_behavior);

		$stmt = MailJobPeer::doCountStmt($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of MailJob objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of MailJob objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		MailJobPeer::addSelectColumns($criteria);
		$startcol2 = (MailJobPeer::NUM_COLUMNS - MailJobPeer::NUM_LAZY_LOAD_COLUMNS);

		kuserPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(MailJobPeer::RECIPIENT_ID, kuserPeer::ID, $join_behavior);

		$stmt = MailJobPeer::doSelectStmt($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = MailJobPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = MailJobPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = MailJobPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				MailJobPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined kuser rows

			$key2 = kuserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = kuserPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = kuserPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					kuserPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (MailJob) to the collection in $obj2 (kuser)
				$obj2->addMailJob($obj1);
			} // if joined row not null

			$results[] = $obj1;
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
	  $dbMap = Propel::getDatabaseMap(BaseMailJobPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseMailJobPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new MailJobTableMap());
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
		return $withPrefix ? MailJobPeer::CLASS_DEFAULT : MailJobPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a MailJob or Criteria object.
	 *
	 * @param      mixed $values Criteria or MailJob object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from MailJob object
		}

		if ($criteria->containsKey(MailJobPeer::ID) && $criteria->keyContainsValue(MailJobPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.MailJobPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a MailJob or Criteria object.
	 *
	 * @param      mixed $values Criteria or MailJob object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(MailJobPeer::ID);
			$selectCriteria->add(MailJobPeer::ID, $criteria->remove(MailJobPeer::ID), $comparison);

		} else { // $values is MailJob object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the mail_job table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(MailJobPeer::TABLE_NAME, $con);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			MailJobPeer::clearInstancePool();
			MailJobPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a MailJob or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or MailJob object or primary key or array of primary keys
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
			$con = Propel::getConnection(MailJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			MailJobPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof MailJob) { // it's a model object
			// invalidate the cache for this single object
			MailJobPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(MailJobPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				MailJobPeer::removeInstanceFromPool($singleval);
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
			MailJobPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given MailJob object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      MailJob $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(MailJob $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(MailJobPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(MailJobPeer::TABLE_NAME);

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

		return BasePeer::doValidate(MailJobPeer::DATABASE_NAME, MailJobPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     MailJob
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = MailJobPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = new Criteria(MailJobPeer::DATABASE_NAME);
		$criteria->add(MailJobPeer::ID, $pk);

		$v = MailJobPeer::doSelect($criteria, $con);

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
			$criteria = new Criteria(MailJobPeer::DATABASE_NAME);
			$criteria->add(MailJobPeer::ID, $pks, Criteria::IN);
			$objs = MailJobPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseMailJobPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseMailJobPeer::buildTableMap();

