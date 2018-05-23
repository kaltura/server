<?php

/**
 * This class is a base class for all indexing objects.
 * It exposes all functions and mapping needed for indexing & searching indexable objects.
 */
abstract class BaseIndexObject
{
	/**
	 * Mapping between indexable field to the matching getter on the propel object.
	 * For example - If the index field 'name' is assigned by the function 'getEscapedName'
	 * then the mapping should be 'name' => 'escapedName'.
	 */
	// protected static $fieldsMap;
	
	/**
	 * Mapping between propel object property to the matching indexable field.
	 * For example - if when searching for entry.name (propel) the condition should apply for entry.full_name (index)
	 * then the mapping should be 'name' => 'full_name'.
	 */
	// protected static $searchableFieldsMap;
	
	/**
	 * Mapping betwen indexable field to its type. 
	 * The list of supported types is defined at IIndexable. 
	 */
	// protected static $typesMap;
	
	/**
	 * List of fields we can filter by 'is null' condition.
	 * For example, if we can query from entry table all records in which last name is null, then
	 * last name should appear in this list.
	 */
	// protected static $nullableFields;

	/**
	 * Mapping between property name to escape type for indexing purposes.
	 * The possible values are taken from SearchIndexFieldEscapeType. 
	 * For example, if when indexing category names we'd like to md5 them, then the
	 * mapping should be 'category_names' => 'SearchIndexFieldEscapeType::MD5_LOWER_CASE'. 
	 */
	// protected static $searchEscapeTypes;
	
	/**
	 * Mapping between property name to escape type for searching purposes.
	 * The possible values are taken from SearchIndexFieldEscapeType.
	 * For example, if when searching for category names we'd like to md5 them first, then the
	 * mapping should be 'category_names' => 'SearchIndexFieldEscapeType::MD5_LOWER_CASE'.
	 */
	// protected static $indexEscapeTypes;
	
	/**
	 * List of index Fields. In Sphinx terminology it means that this is a Field and not an attribute, 
	 * and therefore can be qeuries with 'MATCH'. 
	 */
	// protected static $matchableFields;
	
	/**
	 * List of fields according to which the qeury can be ordered.
	 * For example, if the query can be ordered by 'created_at' than created at should be in this list.
	 */
	// protected static $orderFields;
	
	/**
	 * List of fields indicating whether the query should skip sphinx and go directly to the database. 
	 * For example, if a query on 'entry' contains entry.ID IN (...) going through sphinx does not help 
	 * (unless there is some textual match as well). In this case this list should include entry.ID in it.
	 */
	// protected static $skipFields;
	
	/**
	 * List of fields indicating whether the query should keep the sphinx qeury condition but use it as well when 
	 * querying from the database. 
	 * For example, if a query on 'entry' contains entry.partner_id, we'd like to use the same condition on the database as well.
	 */
	// protected static $conditionToKeep;
	
	/**
	 * Returns the field type by name
	 * @param string $fieldName
	 */
	public static function getFieldType($fieldName, $returnDetailedType = false) {
		$fieldTypes = static::getIndexFieldTypesMap();
		if(!$returnDetailedType && $fieldTypes[$fieldName] == IIndexable::FIELD_TYPE_UINT)
			return IIndexable::FIELD_TYPE_INTEGER;
		
		return $fieldTypes[$fieldName];
	}
	
	/**
	 * Returns whether a given field is nullable
	 * @param string $fieldName
	 */
	public static function isNullableField($fieldName) {
		$nullableFields = static::getIndexNullableList();
		return in_array($fieldName, $nullableFields);
	}

	/**
	 * Returns the matching field escape type for indexing purposes.
	 * @param string $fieldName
	 */
	public static function getIndexFieldsEscapeType($fieldName) {
		$fieldName = self::fixFieldName($fieldName);
		$escapeTypes = static::getIndexFieldsEscapeTypeList();
		if(array_key_exists($fieldName, $escapeTypes))
			return $escapeTypes[$fieldName];
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}
	
	/**
	 * Returns the matching field escape type for searching purposes.
	 * @param string $fieldName
	 */
	public static function getSearchFieldsEscapeType($fieldName) {
		$fieldName = self::fixFieldName($fieldName);
		$escapeTypes = static::getSearchFieldsEscapeTypeList();
		if(array_key_exists($fieldName, $escapeTypes))
			return $escapeTypes[$fieldName];
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}
	
	/**
	 * Returns whether the field is a Field and not an attribute
	 * @param string $fieldName
	 */
	public static function hasMatchableField($fieldName) {
		$matchableFields = static::getIndexMatchableList();
		return in_array($fieldName, $matchableFields);
	}
	
	/**
	 * Returns the index field name
	 * @param string $fieldName
	 */
	public static function getIndexFieldName($columnName) {
		$columnName = self::fixFieldName($columnName);
		$searchableFields = static::getIndexSearchableFieldsMap();
		return $searchableFields[$columnName];
	}
	
	/**
	 * Returns whether a given field is indexed
	 * @param string $fieldName
	 */
	public static function hasIndexFieldName($columnName) {
		$columnName = self::fixFieldName($columnName);
		$searchableFields = static::getIndexSearchableFieldsMap();
		return array_key_exists($columnName, $searchableFields);
	}

	public static function getCompareFieldByApiName($apiName)
	{
		$map = static::getApiCompareAttributesMap();
		return isset($map[$apiName]) ? $map[$apiName] : null;
	}

	public static function getApiNameByCompareField($field)
	{
		$map = static::getApiCompareAttributesMap();
		$apiName = array_search($field, $map, true);
		return $apiName ? $apiName : null;
	}

	public static function getMatchFieldByApiName($apiName)
	{
		$map = static::getApiMatchAttributesMap();
		return isset($map[$apiName]) ? $map[$apiName] : null;
	}

	public static function getApiNameByMatchField($field)
	{
		$map = static::getApiMatchAttributesMap();
		$apiName = array_search($field, $map, true);
		return $apiName ? $apiName : null;
	}
	
	public static function fixFieldName($fieldName) {
		if(strpos($fieldName, '.') === false)
		{
			$indexName = static::getObjectName();
			$fieldName = strtoupper($fieldName);
			$fieldName = $indexName . "." . $fieldName;
		}
		return $fieldName;
	}
	
	public static function getSphinxMatchOptimizations($object) {
		$optimizations = array();
		$sphinxOptimizationMap = static::getSphinxOptimizationValues();
		foreach($sphinxOptimizationMap as $optimization) {
			$format = array_shift($optimization);
			$curOptimization = array();
			foreach($optimization as $curGetter) {
				$getters = explode(".", $curGetter);
				$curValue = call_user_func(array($object,array_shift($getters)));
				while(!empty($getters)) {
					$getter = array_shift($getters);
					if(isset($curValue[$getter])) {
						$curValue = $curValue[$getter];
					} else {
						$curValue = null;
						break;
					}
				}
				if(!is_null($curValue))
					$curOptimization[] = $curValue;
			}
			
			if(!empty($curOptimization))
				$optimizations[] = vsprintf($format,$curOptimization);
		}
		return implode(" ", $optimizations);
	}
	
	/**
	 * Override in order to use the query cache.
	 * Cache invalidation keys are used to determine when cached queries are valid.
	 * Before returning a query result from the cache, the time of the cached query
	 * is compared to the time saved in the invalidation key.
	 * A cached query will only be used if it's newer than the matching invalidation key.
	 *
	 * @return     array The invalidation keys that should be checked before returning a cached result for this criteria.
	 *		 if an empty array is returned, the query cache won't be used - the query will be performed on the Sphinx.
	 *		 When object is null the function returns the invalidation key pattern otherwise it returns the actual value
	 */
	public static function getCacheInvalidationKeys($object = null)
	{
		return array();
	}
}