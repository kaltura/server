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
        categoryKuserPeer::UPDATE_METHOD => "update_method" ,
        categoryKuserPeer::PERMISSION_NAMES => "permission_names" ,
        categoryKuserPeer::PARTNER_ID => "partner_id" ,
        categoryKuserPeer::STATUS => "category_kuser_status" ,
        categoryKuserPeer::CREATED_AT => "created_at" ,
        categoryKuserPeer::UPDATED_AT => "updated_at" ,
	);
	
	public static $sphinxFieldsEscapeType = array(
		categoryKuserPeer::CATEGORY_FULL_IDS => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
	);
	
	public static $sphinxOrderFields = array(
		categoryKuserPeer::CREATED_AT => 'created_at',
		categoryKuserPeer::UPDATED_AT => 'updated_at',
	);
	
	public static $sphinxTypes = array ( 
        		"category_id" => IIndexable::FIELD_TYPE_STRING,
                "kuser_id" => IIndexable::FIELD_TYPE_STRING,
                "screen_name" => IIndexable::FIELD_TYPE_STRING,
                "puser_id" => IIndexable::FIELD_TYPE_STRING,
				"category_full_ids" => IIndexable::FIELD_TYPE_STRING,
				"update_method" => IIndexable::FIELD_TYPE_STRING,
				"permission_names" => IIndexable::FIELD_TYPE_STRING,
				"partner_id" => IIndexable::FIELD_TYPE_STRING,
				"category_kuser_status" => IIndexable::FIELD_TYPE_STRING,
				"created_at" => IIndexable::FIELD_TYPE_DATETIME,
				"updated_at" => IIndexable::FIELD_TYPE_DATETIME,
        );
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getDefaultCriteriaFilter()
	 */
	protected function getDefaultCriteriaFilter() {
		return categoryKuserPeer::getCriteriaFilter();
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
			$fieldName = categoryKuserPeer::TABLE_NAME.".$fieldName";
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
	 * @see SphinxCriteria::getSearchIndexFieldsEscapeType()
	 */
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "category_kuser.$fieldName";
		}
		
		if(!isset(self::$sphinxFieldsEscapeType[$fieldName]))
			return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
			
		return self::$sphinxFieldsEscapeType[$fieldName];
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getFieldPrefix()
	 */
	public function getFieldPrefix ($fieldName)
	{
		KalturaLog::info('fieldname is: $fieldName');
		switch ($fieldName)
		{
			case 'permission_names':
				return categoryKuser::PERMISSION_NAME_FIELD_INDEX_PREFIX. kCurrentContext::getCurrentPartnerId();
			case 'category_kuser_status':
				return categoryKuser::STATUS_FIELD_PREFIX . kCurrentContext::getCurrentPartnerId();
		}

		return parent::getFieldPrefix($fieldName);
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::hasMatchableField()
	 */
	public function hasMatchableField($fieldName) {
		return in_array($fieldName, array(
			"category_id",
			"kuser_id",
			"screen_name",
			"puser_id",
			"category_full_ids",
			"permission_names",
			"category_kuser_status",
			"update_method",
		));
		
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIdField()
	 */
	protected function getSphinxIdField() {
		return 'id';
		
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

	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		if ($filter->get('_in_status'))
		{
			$statusList = explode(',', $filter->get('_in_status'));
			foreach ($statusList as &$status)
			{
				$status = categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::STATUS, $status, kCurrentContext::getCurrentPartnerId());
			}
			$filter->set('_in_status', implode(',', $statusList));
		}
		
		if ($filter->get('_eq_status'))
		{
			$filter->set('_eq_status', categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::STATUS, $filter->get('_eq_status'), kCurrentContext::getCurrentPartnerId()));
		}
		
		if ($filter->get('_in_update_method'))
		{
			$updateMethodList = explode(',', $filter->get('_in_update_method'));
			foreach ($updateMethodList as &$updateMethod)
			{
				$updateMethod = categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::UPDATE_METHOD, $updateMethod, kCurrentContext::getCurrentPartnerId());
			}
			$filter->set('_in_update_method', implode(',', $updateMethodList));
		}
		
		if ($filter->get('_eq_update_method'))
		{
			$filter->set('_eq_update_method', categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::UPDATE_METHOD, $filter->get('_eq_update_method'), kCurrentContext::getCurrentPartnerId()));
		}
		
		if ($filter->get('_matchor_permission_names'))
		{
			$permissionNamesList = explode(',', $filter->get('_matchor_permission_names'));
			foreach ($permissionNamesList as &$permissionName)
			{
				$permissionName = categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionName, kCurrentContext::getCurrentPartnerId());
			}
			$filter->set('_matchor_permission_names', implode(',', $permissionNamesList));
		}

		if ($filter->get('_matchand_permission_names'))
		{
			$permissionNamesList = explode(',', $filter->get('_matchand_permission_names'));
			foreach ($permissionNamesList as &$permissionName)
			{
				$permissionName = categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionName, kCurrentContext::getCurrentPartnerId());
			}
			$filter->set('_matchand_permission_names', implode(',', $permissionNamesList));
		}
		
		if ($filter->get('_notcontains_permission_names'))
		{
			$permissionNamesList = explode(',', $filter->get('_notcontains_permission_names'));
			foreach ($permissionNamesList as &$permissionName)
			{
				$permissionName = categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionName, kCurrentContext::getCurrentPartnerId());
			}
			$filter->set('_notcontains_permission_names', $permissionNamesList);
		}
		
		if ($filter->get('_eq_category_full_ids'))
		{
			$filter->set('_eq_category_full_ids', $filter->get('_eq_category_full_ids').category::FULL_IDS_EQUAL_MATCH_STRING);
		}
		
		return parent::applyFilterFields($filter);
	}
	
	public function translateSphinxCriterion (SphinxCriterion $crit)
	{
		$field = $crit->getTable() . '.' . $crit->getColumn();
		$value = $crit->getValue();
		
		$fieldName = null;
		if ($field == categoryKuserPeer::STATUS)
			$fieldName = categoryKuserPeer::STATUS;

		if ($fieldName)
		{
			$partnerIdCrit = $this->getCriterion(categoryKuserPeer::PARTNER_ID);
			if ($partnerIdCrit && $partnerIdCrit->getComparison() == Criteria::EQUAL)
				$partnerId = $partnerIdCrit->getValue();
			else
				$partnerId = kCurrentContext::getCurrentPartnerId();
			
			if (is_array($value))
			{
				foreach ($value as &$singleValue)
				{
					$singleValue = 	categoryKuser::getSearchIndexFieldValue($fieldName, $singleValue, $partnerId);
				}
			}
			else 
			{
				$value = categoryKuser::getSearchIndexFieldValue($fieldName, $value, $partnerId);
			}
		}

		return array($field, $crit->getComparison(), $value);
	}
	
}