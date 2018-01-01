<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchQueryAttributes
{
	const GLOBAL_SCOPE = "global";
	const INNER_SCOPE = "inner";

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
	 * @var string
	 */
	protected $objectSubType;

	/**
	 * @return string
	 */
	public function getObjectSubType()
	{
		return $this->objectSubType;
	}

	/**
	 * @param string $objectSubType
	 */
	public function setObjectSubType($objectSubType)
	{
		$this->objectSubType = $objectSubType;
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
	 * @return boolean
	 */
	public function getShouldUseDisplayInSearch()
	{
		return $this->shouldUseDisplayInSearch;
	}

	/**
	 * @param boolean $shouldUseDisplayInSearch
	 */
	public function setShouldUseDisplayInSearch($shouldUseDisplayInSearch)
	{
		$this->shouldUseDisplayInSearch = $shouldUseDisplayInSearch;
	}

	/**
	 * @var bool
	 */
	protected $shouldUseDisplayInSearch;

	/**
	 * @var array
	 */
	private $fieldsToHighlight = array(self::GLOBAL_SCOPE => array(), self::INNER_SCOPE => array());
	
	private $scope = self::GLOBAL_SCOPE;

	private $useHighlight = true;

	public function setScopeToInner()
	{
		$this->scope = self::INNER_SCOPE;
		$this->fieldsToHighlight[self::INNER_SCOPE] = array();
	}

	public function setScopeToGlobal()
	{
		$this->scope = self::GLOBAL_SCOPE;
	}

	/**
	 * @return array
	 */
	public function getFieldsToHighlight()
	{
		return $this->fieldsToHighlight[$this->scope];
	}

	/**
	 * @param string $field
	 */
	public function addFieldToHighlight($field)
	{
		if(!array_key_exists($field ,$this->fieldsToHighlight[$this->scope]))
		{
			$this->fieldsToHighlight[$this->scope][$field] = new stdClass();
		}
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

	public function setUseHighlight($useHighlight)
	{
		$this->useHighlight = $useHighlight;
	}

	public function getUseHighlight()
	{
		return $this->useHighlight;
	}
}
