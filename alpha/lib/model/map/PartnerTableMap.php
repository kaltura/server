<?php


/**
 * This class defines the structure of the 'partner' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class PartnerTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.PartnerTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('partner');
		$this->setPhpName('Partner');
		$this->setClassname('Partner');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_NAME', 'PartnerName', 'VARCHAR', false, 256, null);
		$this->addColumn('PARTNER_ALIAS', 'PartnerAlias', 'VARCHAR', false, 64, null);
		$this->addColumn('URL1', 'Url1', 'VARCHAR', false, 1024, null);
		$this->addColumn('URL2', 'Url2', 'VARCHAR', false, 1024, null);
		$this->addColumn('SECRET', 'Secret', 'VARCHAR', false, 50, null);
		$this->addColumn('ADMIN_SECRET', 'AdminSecret', 'VARCHAR', false, 50, null);
		$this->addColumn('MAX_NUMBER_OF_HITS_PER_DAY', 'MaxNumberOfHitsPerDay', 'INTEGER', false, null, -1);
		$this->addColumn('APPEAR_IN_SEARCH', 'AppearInSearch', 'INTEGER', false, null, 2);
		$this->addColumn('DEBUG_LEVEL', 'DebugLevel', 'INTEGER', false, null, 0);
		$this->addColumn('INVALID_LOGIN_COUNT', 'InvalidLoginCount', 'INTEGER', false, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addForeignKey('ANONYMOUS_KUSER_ID', 'AnonymousKuserId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('KS_MAX_EXPIRY_IN_SECONDS', 'KsMaxExpiryInSeconds', 'INTEGER', false, null, 86400);
		$this->addColumn('CREATE_USER_ON_DEMAND', 'CreateUserOnDemand', 'TINYINT', false, null, 1);
		$this->addColumn('PREFIX', 'Prefix', 'VARCHAR', false, 32, null);
		$this->addColumn('ADMIN_NAME', 'AdminName', 'VARCHAR', false, 50, null);
		$this->addColumn('ADMIN_EMAIL', 'AdminEmail', 'VARCHAR', false, 50, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 1024, null);
		$this->addColumn('COMMERCIAL_USE', 'CommercialUse', 'TINYINT', false, null, 0);
		$this->addColumn('MODERATE_CONTENT', 'ModerateContent', 'TINYINT', false, null, 0);
		$this->addColumn('NOTIFY', 'Notify', 'TINYINT', false, null, 0);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('SERVICE_CONFIG_ID', 'ServiceConfigId', 'VARCHAR', false, 64, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, 1);
		$this->addColumn('CONTENT_CATEGORIES', 'ContentCategories', 'VARCHAR', false, 1024, null);
		$this->addColumn('TYPE', 'Type', 'TINYINT', false, null, 1);
		$this->addColumn('PHONE', 'Phone', 'VARCHAR', false, 64, null);
		$this->addColumn('DESCRIBE_YOURSELF', 'DescribeYourself', 'VARCHAR', false, 64, null);
		$this->addColumn('ADULT_CONTENT', 'AdultContent', 'TINYINT', false, null, 0);
		$this->addColumn('PARTNER_PACKAGE', 'PartnerPackage', 'TINYINT', false, null, 1);
		$this->addColumn('USAGE_PERCENT', 'UsagePercent', 'INTEGER', false, null, 0);
		$this->addColumn('STORAGE_USAGE', 'StorageUsage', 'INTEGER', false, null, 0);
		$this->addColumn('EIGHTY_PERCENT_WARNING', 'EightyPercentWarning', 'INTEGER', false, null, null);
		$this->addColumn('USAGE_LIMIT_WARNING', 'UsageLimitWarning', 'INTEGER', false, null, null);
		$this->addColumn('MONITOR_USAGE', 'MonitorUsage', 'INTEGER', false, null, 1);
		$this->addColumn('PRIORITY_GROUP_ID', 'PriorityGroupId', 'INTEGER', false, null, null);
		$this->addColumn('PARTNER_GROUP_TYPE', 'PartnerGroupType', 'SMALLINT', false, null, 1);
		$this->addColumn('PARTNER_PARENT_ID', 'PartnerParentId', 'INTEGER', false, null, null);
		$this->addColumn('KMC_VERSION', 'KmcVersion', 'VARCHAR', false, 15, '1');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('anonymous_kuser_id' => 'id', ), null, null);
    $this->addRelation('adminKuser', 'adminKuser', RelationMap::ONE_TO_MANY, array('id' => 'partner_id', ), null, null);
    $this->addRelation('SphinxLog', 'SphinxLog', RelationMap::ONE_TO_MANY, array('id' => 'partner_id', ), null, null);
	} // buildRelations()

} // PartnerTableMap
