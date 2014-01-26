<?php


/**
 * This class defines the structure of the 'drm_policy' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.drm
 * @subpackage model.map
 */
class DrmPolicyTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.drm.DrmPolicyTableMap';

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
		$this->setName('drm_policy');
		$this->setPhpName('DrmPolicy');
		$this->setClassname('DrmPolicy');
		$this->setPackage('plugins.drm');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('PROFILE_ID', 'ProfileId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'LONGVARCHAR', true, null, null);
		$this->addColumn('SYSTEM_NAME', 'SystemName', 'VARCHAR', true, 128, '');
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('PROVIDER', 'Provider', 'INTEGER', true, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', true, null, null);
		$this->addColumn('SCENARIO', 'Scenario', 'INTEGER', true, null, null);
		$this->addColumn('LICENSE_TYPE', 'LicenseType', 'INTEGER', false, null, null);
		$this->addColumn('LICENSE_EXPIRATION_POLICY', 'LicenseExpirationPolicy', 'INTEGER', false, null, null);
		$this->addColumn('DURATION', 'Duration', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // DrmPolicyTableMap
