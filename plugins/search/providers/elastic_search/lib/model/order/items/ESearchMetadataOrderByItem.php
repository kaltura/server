<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
class ESearchMetadataOrderByItem extends ESearchOrderByItem
{

	const NESTED_PATH = 'nested_path';
	const NESTED_FILTER = 'nested_filter';
	const NESTED_DOC_PATH = 'metadata';
	const PID = 'pid';
	const XPATH = 'xpath';

	/**
	 *  @var string
	 */
	protected $xpath;

	/**
	 *  @var int
	 */
	protected $metadataProfileId;

	/**
	 * @return string
	 */
	public function getXpath()
	{
		return $this->xpath;
	}

	/**
	 * @param string $xpath
	 */
	public function setXpath($xpath)
	{
		$this->xpath = $xpath;
	}

	/**
	 * @return int
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	public function getSortField()
	{
		return self::PID.$this->getMetadataProfileId().self::XPATH.$this->getXpath();
	}

	public function getSortConditions()
	{
		if(!$this->getXpath() || !$this->getMetadataProfileId())
			throw new kESearchException('empty search items are not allowed', kESearchException::MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM);

		$bool = new kESearchBoolQuery();
		$xpath = elasticSearchUtils::formatSearchTerm($this->getXpath());
		$xpathQuery = new kESearchTermQuery(ESearchMetadataFieldName::XPATH, $xpath);
		$bool->addToFilter($xpathQuery);
		$profileIdQuery = new kESearchTermQuery(ESearchMetadataFieldName::PROFILE_ID, $this->getMetadataProfileId());
		$bool->addToFilter($profileIdQuery);

		$nestedFilter = $bool->getFinalQuery();

		$conditions = array();
		$intCondition = array(
			ESearchMetadataOrderByFieldName::METADATA_VALUE_INT => array(
				self::NESTED_PATH => self::NESTED_DOC_PATH,
				self::ORDER => $this->getSortOrder(),
				self::NESTED_FILTER => $nestedFilter
			)
		);
		$conditions[] = $intCondition;
		$textCondition = array(
			ESearchMetadataOrderByFieldName::METADATA_VALUE_TEXT => array(
				self::NESTED_PATH => self::NESTED_DOC_PATH,
				self::ORDER => $this->getSortOrder(),
				self::NESTED_FILTER => $nestedFilter
			)
		);
		$conditions[] = $textCondition;

		return $conditions;
	}

}
