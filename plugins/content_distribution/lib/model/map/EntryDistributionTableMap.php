<?php


/**
 * This class defines the structure of the 'entry_distribution' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.contentDistribution
 * @subpackage model.map
 */
class EntryDistributionTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.contentDistribution.EntryDistributionTableMap';

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
		$this->setName('entry_distribution');
		$this->setPhpName('EntryDistribution');
		$this->setClassname('EntryDistribution');
		$this->setPackage('plugins.contentDistribution');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('SUBMITTED_AT', 'SubmittedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('ENTRY_ID', 'EntryId', 'VARCHAR', false, 20, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('DISTRIBUTION_PROFILE_ID', 'DistributionProfileId', 'INTEGER', false, null, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('DIRTY_STATUS', 'DirtyStatus', 'TINYINT', false, null, null);
		$this->addColumn('THUMB_ASSET_IDS', 'ThumbAssetIds', 'VARCHAR', false, 255, null);
		$this->addColumn('FLAVOR_ASSET_IDS', 'FlavorAssetIds', 'VARCHAR', false, 255, null);
		$this->addColumn('SUNRISE', 'Sunrise', 'TIMESTAMP', false, null, null);
		$this->addColumn('SUNSET', 'Sunset', 'TIMESTAMP', false, null, null);
		$this->addColumn('REMOTE_ID', 'RemoteId', 'VARCHAR', false, 31, null);
		$this->addColumn('PLAYS', 'Plays', 'INTEGER', false, null, null);
		$this->addColumn('VIEWS', 'Views', 'INTEGER', false, null, null);
		$this->addColumn('VALIDATION_ERRORS', 'ValidationErrors', 'LONGVARCHAR', false, null, null);
		$this->addColumn('ERROR_TYPE', 'ErrorType', 'INTEGER', false, null, null);
		$this->addColumn('ERROR_NUMBER', 'ErrorNumber', 'INTEGER', false, null, null);
		$this->addColumn('ERROR_DESCRIPTION', 'ErrorDescription', 'VARCHAR', false, 255, null);
		$this->addColumn('LAST_REPORT', 'LastReport', 'TIMESTAMP', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // EntryDistributionTableMap
