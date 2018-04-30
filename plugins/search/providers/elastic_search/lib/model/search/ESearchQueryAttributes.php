<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchQueryAttributes
{
	/**
	 * @var array
	 */
	protected $partnerLanguages;

	/**
	 * @var int
	 */
	protected $overrideInnerHitsSize;

	/**
	 * @var string
	 */
	protected $objectId;

	/**
	 * @var ESearchBaseQueryFilterAttributes
	 */
	protected $queryFilterAttributes;

	/**
	 * @var bool
	 */
	protected $nestedOperatorContext;

	/**
	 * @var bool
	 */
	protected $initNestedQuery;

	/**
	 * @var string
	 */
	protected $nestedOperatorPath;

	/***
	 * @var string
	 */
	protected $nestedOperatorInnerHitsSize;

	/**
	 * @var int
	 */
	protected $nestedOperatorNumOfFragments;

	/**
	 * @var string
	 */
	protected $nestedQueryName;

	/**
	 * @var int
	 */
	protected $nestedQueryNameIndex = 0;

	/**
	 * @var array
	 */
	protected $nestedOperatorObjectTypes;

	/**
	 * @var array
	 */
	protected $nestedQuerySortOrder;

	/**
	 * @var ESearchQueryHighlightsAttributes
	 */
	private $queryHighlightsAttributes;

	function __construct()
	{
		$this->queryHighlightsAttributes = new ESearchQueryHighlightsAttributes();
		$this->queryFilterAttributes = new ESearchEntryQueryFilterAttributes();
	}

	/**
	 * @return string
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}

	/**
	 * @return array
	 */
	public function getPartnerLanguages()
	{
		return $this->partnerLanguages;
	}

	/**
	 * @param array $partnerLanguages
	 */
	public function setPartnerLanguages($partnerLanguages)
	{
		$this->partnerLanguages = $partnerLanguages;
	}

	/**
	 * @return int
	 */
	public function getOverrideInnerHitsSize()
	{
		return $this->overrideInnerHitsSize;
	}

	/**
	 * @param int $overrideInnerHitsSize
	 */
	public function setOverrideInnerHitsSize($overrideInnerHitsSize)
	{
		$this->overrideInnerHitsSize = $overrideInnerHitsSize;
	}

	/**
	 * @return boolean
	 */
	public function isNestedOperatorContext()
	{
		return $this->nestedOperatorContext;
	}

	/**
	 * @param boolean $nestedOperatorContext
	 */
	public function setNestedOperatorContext($nestedOperatorContext)
	{
		$this->nestedOperatorContext = $nestedOperatorContext;
	}

	/**
	 * @return boolean
	 */
	public function isInitNestedQuery()
	{
		return $this->initNestedQuery;
	}

	/**
	 * @param boolean $initNestedQuery
	 */
	public function setInitNestedQuery($initNestedQuery)
	{
		$this->initNestedQuery = $initNestedQuery;
	}

	/**
	 * @return string
	 */
	public function getNestedOperatorPath()
	{
		return $this->nestedOperatorPath;
	}

	/**
	 * @param string $nestedOperatorPath
	 */
	public function setNestedOperatorPath($nestedOperatorPath)
	{
		$this->nestedOperatorPath = $nestedOperatorPath;
	}

	/**
	 * @return string
	 */
	public function getNestedOperatorInnerHitsSize()
	{
		return $this->nestedOperatorInnerHitsSize;
	}

	/**
	 * @param string $nestedOperatorInnerHitsSize
	 */
	public function setNestedOperatorInnerHitsSize($nestedOperatorInnerHitsSize)
	{
		$this->nestedOperatorInnerHitsSize = $nestedOperatorInnerHitsSize;
	}

	/**
	 * @return int
	 */
	public function getNestedOperatorNumOfFragments()
	{
		return $this->nestedOperatorNumOfFragments;
	}

	/**
	 * @param int $nestedOperatorNumOfFragments
	 */
	public function setNestedOperatorNumOfFragments($nestedOperatorNumOfFragments)
	{
		$this->nestedOperatorNumOfFragments = $nestedOperatorNumOfFragments;
	}

	/**
	 * @return string
	 */
	public function getNestedQueryName()
	{
		return $this->nestedQueryName;
	}

	/**
	 * @param string $nestedQueryName
	 */
	public function setNestedQueryName($nestedQueryName)
	{
		$this->nestedQueryName = $nestedQueryName;
	}

	/**
	 * @return int
	 */
	public function getNestedQueryNameIndex()
	{
		return $this->nestedQueryNameIndex;
	}

	public function incrementNestedQueryNameIndex()
	{
		$this->nestedQueryNameIndex++;
	}

	public function resetNestedOperatorObjectTypes()
	{
		$this->nestedOperatorObjectTypes = array();
	}

	public function addToNestedOperatorObjectTypes($type)
	{
		$this->nestedOperatorObjectTypes[$type] = true;
	}

	public function validateNestedOperatorObjectTypes()
	{
		if(isset($this->nestedOperatorObjectTypes[ESearchNestedOperator::ESEARCH_NESTED_OPERATOR]))
			unset($this->nestedOperatorObjectTypes[ESearchNestedOperator::ESEARCH_NESTED_OPERATOR]);

		if(count($this->nestedOperatorObjectTypes) == 1)
			return true;
		return false;
	}

	public function getQueryHighlightsAttributes()
	{
		return $this->queryHighlightsAttributes;
	}

	/**
	 * @return ESearchBaseQueryFilterAttributes queryFilterAttributes
	 */
	public function getQueryFilterAttributes()
	{
		return $this->queryFilterAttributes;
	}

	public function setQueryFilterAttributes($queryFilterAttributes)
	{
		$this->queryFilterAttributes = $queryFilterAttributes;
	}

	/**
	 * @return array
	 */
	public function getNestedQuerySortOrder()
	{
		return $this->nestedQuerySortOrder;
	}

	/**
	 * @param array $nestedQuerySortOrder
	 */
	public function setNestedQuerySortOrder($nestedQuerySortOrder)
	{
		$this->nestedQuerySortOrder = $nestedQuerySortOrder;
	}

}
