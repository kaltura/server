<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchMetadataItem extends ESearchItem
{

	private static $allowed_search_types_for_field = array(
		'metadata.value_text' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN,'Unified'),
		'metadata.value_int' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN ),
	);

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var string;
	 */
	protected $xpath;

	/**
	 * @var int;
	 */
	protected $metadataProfileId;


	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public function getType()
	{
		return 'metadata';
	}

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

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$metadataQuery['nested']['path'] = 'metadata';
		$metadataQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
		$allowedSearchTypes = ESearchCuePointItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $metadataESearchItem)
		{
			/* @var ESearchMetadataItem $metadataESearchItem */
			self::createSingleItemQuery($boolOperator, $metadataESearchItem, $metadataQuery, $allowedSearchTypes);
			if ($metadataESearchItem->getXpath())
			{
				$metadataQuery['nested']['query']['bool'][$boolOperator][] = array(
					'term' => array(
						'metadata.xpath' => strtolower($metadataESearchItem->getXpath())
					)
				);
			}
			if ($metadataESearchItem->getMetadataProfileId())
			{
				$metadataQuery['nested']['query']['bool'][$boolOperator][] = array(
					'term' => array(
						'metadata.metadata_profile_id' => strtolower($metadataESearchItem->getMetadataProfileId())
					)
				);
				return $metadataQuery;
			}
		}
		return $metadataQuery;
	}

	public static function createSingleItemQuery($boolOperator, $metadataESearchItem, &$metadataQuery, $allowedSearchTypes)
	{
		switch ($metadataESearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($metadataESearchItem,'metadata.value_text.raw', $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($metadataESearchItem, 'metadata.value_text', false);
				break;
			case ESearchItemType::STARTS_WITH:
				$metadataQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($metadataESearchItem, 'metadata.value_text.raw', $allowedSearchTypes);
				break;
			case ESearchItemType::DOESNT_CONTAIN:
				$metadataQuery['nested']['query']['bool']['must_not'][] =
					kESearchQueryManager::getDoesntContainQuery($metadataESearchItem, 'metadata.value_text.raw', $allowedSearchTypes);
		}

	}

}