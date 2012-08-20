<?php


/**
 * This class defines the structure of the 'category' table.
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
class categoryTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'Core.categoryTableMap';

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
		$this->setName('category');
		$this->setPhpName('category');
		$this->setClassname('category');
		$this->setPackage('Core');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('PARENT_ID', 'ParentId', 'INTEGER', true, null, null);
		$this->addColumn('DEPTH', 'Depth', 'TINYINT', true, null, null);
		$this->addColumn('PARTNER_ID', 'PartnerId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, '');
		$this->addColumn('FULL_NAME', 'FullName', 'LONGVARCHAR', true, null, null);
		$this->addColumn('FULL_IDS', 'FullIds', 'LONGVARCHAR', true, null, null);
		$this->addColumn('ENTRIES_COUNT', 'EntriesCount', 'INTEGER', true, null, 0);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('STATUS', 'Status', 'INTEGER', false, null, null);
		$this->addColumn('DIRECT_ENTRIES_COUNT', 'DirectEntriesCount', 'INTEGER', false, null, 0);
		$this->addColumn('DIRECT_SUB_CATEGORIES_COUNT', 'DirectSubCategoriesCount', 'INTEGER', false, null, 0);
		$this->addColumn('MEMBERS_COUNT', 'MembersCount', 'INTEGER', false, null, 0);
		$this->addColumn('PENDING_MEMBERS_COUNT', 'PendingMembersCount', 'INTEGER', false, null, 0);
		$this->addColumn('PENDING_ENTRIES_COUNT', 'PendingEntriesCount', 'INTEGER', false, null, 0);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TAGS', 'Tags', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DISPLAY_IN_SEARCH', 'DisplayInSearch', 'TINYINT', false, null, 1);
		$this->addColumn('PRIVACY', 'Privacy', 'TINYINT', false, null, 1);
		$this->addColumn('INHERITANCE_TYPE', 'InheritanceType', 'TINYINT', false, null, 2);
		$this->addColumn('USER_JOIN_POLICY', 'UserJoinPolicy', 'TINYINT', false, null, 3);
		$this->addColumn('DEFAULT_PERMISSION_LEVEL', 'DefaultPermissionLevel', 'TINYINT', false, null, 3);
		$this->addColumn('KUSER_ID', 'KuserId', 'INTEGER', false, null, null);
		$this->addColumn('PUSER_ID', 'PuserId', 'VARCHAR', false, 100, null);
		$this->addColumn('REFERENCE_ID', 'ReferenceId', 'VARCHAR', false, 512, null);
		$this->addColumn('CONTRIBUTION_POLICY', 'ContributionPolicy', 'TINYINT', false, null, 2);
		$this->addColumn('CUSTOM_DATA', 'CustomData', 'LONGVARCHAR', false, null, null);
		$this->addColumn('PRIVACY_CONTEXT', 'PrivacyContext', 'VARCHAR', false, 255, null);
		$this->addColumn('PRIVACY_CONTEXTS', 'PrivacyContexts', 'VARCHAR', false, 255, null);
		$this->addColumn('INHERITED_PARENT_ID', 'InheritedParentId', 'INTEGER', false, null, null);
		$this->addColumn('MODERATION', 'Moderation', 'BOOLEAN', false, null, false);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('categoryKuser', 'categoryKuser', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), null, null);
	} // buildRelations()

} // categoryTableMap
