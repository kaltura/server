<?php


/**
 * This class defines the structure of the 'delivery_profile' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package Core
 * @subpackage model.map
 */
class deliveryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.deliveryTableMap';

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
		$this->setName('delivery_profile');
		$this->setPhpName('delivery');
		$this->setClassname('delivery');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('TYPE', 'Type', 'INTEGER', true, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', false, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 128, null);
		$this->addColumn('SYSTEM_NAME', 'SystemName', 'VARCHAR', false, 128, null);
		$this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 128, null);
		$this->addColumn('URL', 'Url', 'VARCHAR', false, 256, null);
		$this->addColumn('HOST_NAME', 'HostName', 'VARCHAR', false, 127, null);
		$this->addColumn('IS_DEFAULT', 'IsDefault', 'BOOLEAN', false, null, null);
		$this->addColumn('IS_SECURE', 'IsSecure', 'BOOLEAN', false, null, null);
		$this->addColumn('PARENT_ID', 'ParentId', 'INTEGER', false, null, 0);
		$this->addColumn('RECOGNIZER', 'Recognizer', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TOKENIZER', 'Tokenizer', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DELIVERY_STATUS', 'DeliveryStatus', 'INTEGER', false, null, null);
		$this->addColumn('STREAMER_TYPE', 'StreamerType', 'VARCHAR', false, 30, null);
		$this->addColumn('MEDIA_PROTOCOLS', 'MediaProtocols', 'VARCHAR', false, 256, null);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // deliveryTableMap
