<?php


/**
 * This class defines the structure of the 'storage_profile' table.
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
class StorageProfileTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.StorageProfileTableMap';

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
		$this->setName('storage_profile');
		$this->setPhpName('StorageProfile');
		$this->setClassname('StorageProfile');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 31, null);
		$this->addColumn('DESCIPTION', 'Desciption', 'VARCHAR', false, 127, null);
		$this->addColumn('STATUS', 'Status', 'TINYINT', false, null, null);
		$this->addColumn('PROTOCOL', 'Protocol', 'TINYINT', false, null, null);
		$this->addColumn('STORAGE_URL', 'StorageUrl', 'VARCHAR', false, 127, null);
		$this->addColumn('STORAGE_BASE_DIR', 'StorageBaseDir', 'VARCHAR', false, 127, null);
		$this->addColumn('STORAGE_USERNAME', 'StorageUsername', 'VARCHAR', false, 31, null);
		$this->addColumn('STORAGE_PASSWORD', 'StoragePassword', 'VARCHAR', false, 31, null);
		$this->addColumn('STORAGE_FTP_PASSIVE_MODE', 'StorageFtpPassiveMode', 'TINYINT', false, null, null);
		$this->addColumn('DELIVERY_HTTP_BASE_URL', 'DeliveryHttpBaseUrl', 'VARCHAR', false, 127, null);
		$this->addColumn('DELIVERY_RMP_BASE_URL', 'DeliveryRmpBaseUrl', 'VARCHAR', false, 127, null);
		$this->addColumn('DELIVERY_IIS_BASE_URL', 'DeliveryIisBaseUrl', 'VARCHAR', false, 127, null);
		$this->addColumn('MIN_FILE_SIZE', 'MinFileSize', 'INTEGER', false, null, null);
		$this->addColumn('MAX_FILE_SIZE', 'MaxFileSize', 'INTEGER', false, null, null);
		$this->addColumn('FLAVOR_PARAMS_IDS', 'FlavorParamsIds', 'VARCHAR', false, 127, null);
		$this->addColumn('MAX_CONCURRENT_CONNECTIONS', 'MaxConcurrentConnections', 'INTEGER', false, null, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('PATH_MANAGER_CLASS', 'PathManagerClass', 'VARCHAR', false, 127, null);
		$this->addColumn('URL_MANAGER_CLASS', 'UrlManagerClass', 'VARCHAR', false, 127, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // StorageProfileTableMap
