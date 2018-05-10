<?php

/**
 * This class is a container class for all indexing properties about
 * a single indexable field
 * Further documentation can be found in BaseIndexObject.
 */
class IndexableField {
	
	/** The property name (Propel name) */
	public $name; 	
	
	/** The index name (Sphinx name) */	
	public $indexName; 
	
	/** The index type. (Values are the string values of IIndexable options) */	
	public $type;
	
	/** The matching getter on the propel object. */
	public $getter;
	
	/**  Whether the property can be filtered by 'is null' condition */
	public $nullable = false;

	/**  Whether the property can be enchriched by 'enrich getter'*/
	public $enrichable = false;

	/**  Whether the query can be ordered by this field */
	public $orderable = false;
	
	/** Whether this field exist only in the index and not as property of the original object 
	 * (Usually indicates an optimization field) */
	public $searchOnly = false;
	
	/** If indicated, we can skip the Index search and go directly to the DB */
	public $skipField = false;
	
	/** If indicated then the field is a Field and not a property */
	public $matchable = false;

	/** The escaping one should use to index this field. */
	public $indexEscapeType = null;
	
	/** The escaping one should use to search this field. */
	public $searchEscapeType = null;
	
	/** Whether we keep this field condition for the DB searching as well*/
	public $keepCondition = false; 
	
	/** Whether the field is both sphinx Field and Attribute*/
	public $sphinxStringAttribute = "field";

	/** Api name for this field */
	public $apiName = null;

	public function __construct($name, $index, $type) {
		$this->name = $name;
		$this->indexName = $index;
		$this->type = $type;
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $getter
	 */
	public function getGetter() {
		return $this->getter;
	}

	/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return the $nullable
	 */
	public function getNullable() {
		return $this->nullable;
	}

	/**
	 * @return the $orederable
	 */
	public function getOrderable() {
		return $this->orderable;
	}

	/**
	 * @return the $enrichable
	 */
	public function getEnrichable() {
		return $this->enrichable;
	}

	/**
	 * @return the $searchOnly
	 */
	public function getSearchOnly() {
		return $this->searchOnly;
	}

	/**
	 * @return the $skipField
	 */
	public function getSkipField() {
		return $this->skipField;
	}

	/**
	 * @return the $matchable
	 */
	public function getMatchable() {
		return $this->matchable;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $getter
	 */
	public function setGetter($getter) {
		$this->getter = $getter;
	}

	/**
	 * @param field_type $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param field_type $nullable
	 */
	public function setNullable($nullable) {
		$this->nullable = $nullable;
	}

	/**
	 * @param field_type $enrichable
	 */
	public function setEnrichable($enrichable) {
		$this->enrichable = $enrichable;
	}

	/**
	 * @param field_type $orederable
	 */
	public function setOrderable($orderable) {
		$this->orderable = $orderable;
	}

	/**
	 * @param field_type $searchOnly
	 */
	public function setSearchOnly($searchOnly) {
		$this->searchOnly = $searchOnly;
	}

	/**
	 * @param field_type $skipField
	 */
	public function setSkipField($skipField) {
		$this->skipField = $skipField;
	}

	/**
	 * @param field_type $matchable
	 */
	public function setMatchable($matchable) {
		$this->matchable = $matchable;
	}
	
	/**
	 * @return the $indexEscapeType
	 */
	public function getIndexEscapeType() {
		return $this->indexEscapeType;
	}

	/**
	 * @return the $searchEscapeType
	 */
	public function getSearchEscapeType() {
		return $this->searchEscapeType;
	}

	/**
	 * @param NULL $indexEscapeType
	 */
	public function setIndexEscapeType($indexEscapeType) {
		$this->indexEscapeType = $indexEscapeType;
	}

	/**
	 * @param NULL $searchEscapeType
	 */
	public function setSearchEscapeType($searchEscapeType) {
		$this->searchEscapeType = $searchEscapeType;
	}

	/**
	 * @return the $indexName
	 */
	public function getIndexName() {
		return $this->indexName;
	}

	/**
	 * @param field_type $indexName
	 */
	public function setIndexName($indexName) {
		$this->indexName = $indexName;
	}
	
	/**
	 * @return the $keepCondition
	 */
	public function getKeepCondition() {
		return $this->keepCondition;
	}

	/**
	 * @param boolean $keepCondition
	 */
	public function setKeepCondition($keepCondition) {
		$this->keepCondition = $keepCondition;
	}
	
	/**
	 * @return the $sphinxStringAttribute
	 */
	public function getSphinxStringAttribute() {
		return $this->sphinxStringAttribute;
	}

	/**
	 * @param boolean $sphinxStringAttribute
	 */
	public function setSphinxStringAttribute($sphinxStringAttribute) {
		$this->sphinxStringAttribute = $sphinxStringAttribute;
	}

	/**
	 * @return string
	 */
	public function getApiName()
	{
		return $this->apiName;
	}

	/**
	 * @param string $apiName
	 */
	public function setApiName($apiName)
	{
		$this->apiName = $apiName;
	}
}

