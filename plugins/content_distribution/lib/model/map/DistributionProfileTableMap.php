<?php


/**
 * This class defines the structure of the 'distribution_profile' table.
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
class DistributionProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.DistributionProfileTableMap';

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
		$this->setName('distribution_profile');
		$this->setPhpName('DistributionProfile');
		$this->setClassname('DistributionProfile');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('PROVIDER_TYPE', 'ProviderType', 'INTEGER', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 31, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('SUBMIT_ENABLED', 'SubmitEnabled', 'TINYINT', false, null, null);
		$this->addColumn('UPDATE_ENABLED', 'UpdateEnabled', 'TINYINT', false, null, null);
		$this->addColumn('DELETE_ENABLED', 'DeleteEnabled', 'TINYINT', false, null, null);
		$this->addColumn('REPORT_ENABLED', 'ReportEnabled', 'TINYINT', false, null, null);
		$this->addColumn('AUTO_CREATE_FLAVORS', 'AutoCreateFlavors', 'VARCHAR', false, 255, null);
		$this->addColumn('AUTO_CREATE_THUMB', 'AutoCreateThumb', 'VARCHAR', false, 255, null);
		$this->addColumn('OPTIONAL_FLAVOR_PARAMS_IDS', 'OptionalFlavorParamsIds', 'VARCHAR', false, 127, null);
		$this->addColumn('REQUIRED_FLAVOR_PARAMS_IDS', 'RequiredFlavorParamsIds', 'VARCHAR', false, 127, null);
		$this->addColumn('OPTIONAL_THUMB_DIMENSIONS', 'OptionalThumbDimensions', 'VARCHAR', false, 2048, null);
		$this->addColumn('REQUIRED_THUMB_DIMENSIONS', 'RequiredThumbDimensions', 'VARCHAR', false, 2048, null);
		$this->addColumn('REPORT_INTERVAL', 'ReportInterval', 'INTEGER', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // DistributionProfileTableMap
