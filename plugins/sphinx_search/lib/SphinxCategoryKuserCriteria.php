<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxCategoryKuserCriteria extends SphinxCriteria
{
	
	 public static $sphinxFields = array(
		categoryKuserPeer::CATEGORY_ID => "category_id",
        categoryKuserPeer::KUSER_ID=> "kuser_id",
        categoryKuserPeer::SCREEN_NAME => "screen_name" ,
        categoryKuserPeer::PUSER_ID => "puser_id" ,
        categoryKuserPeer::CATEGORY_FULL_IDS => "category_full_ids" ,
        categoryKuserPeer::INHERIT_FROM_CATEGORY => "inherit_from_category" ,
        categoryKuserPeer::UPDATE_METHOD => "update_method" ,
        categoryKuserPeer::PERMISSION_NAMES => "permission_names" ,
        categoryKuserPeer::PARTNER_ID => "partner_id" ,
        categoryKuserPeer::STATUS => "status" ,
        categoryKuserPeer::CREATED_AT => "created_at" ,
        categoryKuserPeer::UPDATED_AT => "updated_at" ,
	);
	
	public static $sphinxOrderFields = array(
		categoryKuserPeer::CREATED_AT => 'created_at',
		categoryKuserPeer::UPDATED_AT => 'updated_at',
	);
	
	public static $sphinxTypes = array ( 
        		"category_id" => IIndexable::FIELD_TYPE_INTEGER,
                "kuser_id" => IIndexable::FIELD_TYPE_INTEGER,
                "screen_name" => IIndexable::FIELD_TYPE_STRING,
                "puser_id" => IIndexable::FIELD_TYPE_STRING,
				"category_full_ids" => IIndexable::FIELD_TYPE_STRING,
				"inherit_from_category" => IIndexable::FIELD_TYPE_INTEGER,
				"update_method" => IIndexable::FIELD_TYPE_INTEGER,
				"permission_names" => IIndexable::FIELD_TYPE_STRING,
				"partner_id" => IIndexable::FIELD_TYPE_INTEGER,
				"status" => IIndexable::FIELD_TYPE_INTEGER,
				"created_at" => IIndexable::FIELD_TYPE_DATETIME,
				"updated_at" => IIndexable::FIELD_TYPE_DATETIME,
        );
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getDefaultCriteriaFilter()
	 */
	protected function getDefaultCriteriaFilter() {
		return kuserPeer::getCriteriaFilter();
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIndexName()
	 */
	protected function getSphinxIndexName() {
		return kSphinxSearchManager::getSphinxIndexName(categoryKuserPeer::TABLE_NAME);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxOrderFields()
	 */
	public function getSphinxOrderFields() {
		return self::$sphinxOrderFields;
		
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::hasSphinxFieldName()
	 */
	public function hasSphinxFieldName($fieldName) 
	{
		
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = kuserPeer::TABLE_NAME.".$fieldName";
		}
			
		return isset(self::$sphinxFields[$fieldName]);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxFieldName()
	 */
	public function getSphinxFieldName($fieldName) 
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = categoryKuserPeer::TABLE_NAME.".$fieldName";
		}
			
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];	
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxFieldType()
	 */
	public function getSphinxFieldType($fieldName) {
		if(!isset(self::$sphinxTypes[$fieldName]))
			return null;
			
		return self::$sphinxTypes[$fieldName];
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::hasMatchableField()
	 */
	public function hasMatchableField($fieldName) {
		return in_array($fieldName, array(
			"screen_name",
			"puser_id",
			"category_full_ids",
			"permission_names",
		));
		
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIdField()
	 */
	protected function getSphinxIdField() {
		return 'int_id';
		
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getPropelIdField()
	 */
	protected function getPropelIdField() {
		return categoryKuserPeer::ID;
		
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::doCountOnPeer()
	 */
	protected function doCountOnPeer(Criteria $c) {
		return categoryKuserPeer::doCount($c);
	}

	
}