<?php


/**
 * This class defines the structure of the 'sphinx_log_server' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.search
 * @subpackage model.map
 */
class SphinxLogServerTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.search.SphinxLogServerTableMap';

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
		$this->setName('sphinx_log_server');
		$this->setPhpName('SphinxLogServer');
		$this->setClassname('SphinxLogServer');
		$this->setPackage('plugins.search');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('SERVER', 'Server', 'VARCHAR', false, 63, null);
		$this->addColumn('DC', 'Dc', 'INTEGER', false, null, null);
		$this->addForeignKey('LAST_LOG_ID', 'LastLogId', 'INTEGER', 'sphinx_log', 'ID', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('SphinxLog', 'SphinxLog', RelationMap::MANY_TO_ONE, array('last_log_id' => 'id', ), null, null);
	} // buildRelations()

} // SphinxLogServerTableMap
