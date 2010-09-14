<?php


/**
 * This class defines the structure of the 'alert' table.
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
class alertTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.alertTableMap';

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
		$this->setName('alert');
		$this->setPhpName('alert');
		$this->setClassname('alert');
		$this->setPackage('lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('KUSER_ID', 'KuserId', 'INTEGER', 'kuser', 'ID', false, null, null);
		$this->addColumn('ALERT_TYPE', 'AlertType', 'INTEGER', false, null, null);
		$this->addColumn('SUBJECT_ID', 'SubjectId', 'INTEGER', false, null, null);
		$this->addColumn('RULE_TYPE', 'RuleType', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('kuser', 'kuser', RelationMap::MANY_TO_ONE, array('kuser_id' => 'id', ), null, null);
	} // buildRelations()

} // alertTableMap
