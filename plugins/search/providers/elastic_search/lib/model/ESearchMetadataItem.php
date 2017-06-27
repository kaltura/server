<?php

class ESearchMetadataItem extends ESearchItem
{

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

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $additionalParams = null)
	{
		$metadataQuery['nested']['path'] = 'metadata';
		$metadataQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
		foreach ($eSearchItemsArr as $metadataESearchItem)
		{
			/* @var ESearchMetadataItem $metadataESearchItem */
			switch ($metadataESearchItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$metadataQuery['nested']['query']['bool'][$boolOperator][] = array(
						'term' => array(
							'metadata.value_text.raw' => strtolower($metadataESearchItem->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::PARTIAL:
					$captionQuery['nested']['query']['bool'][$boolOperator][] = array(
						'multi_match' => array(
							'query' => strtolower($metadataESearchItem->getSearchTerm()),
							'fields' => array(
								'metadata.value_text.trigram',
								'metadata.value_text',
							),
							'type' => 'most_fields'
						)
					);
					break;
				case ESearchItemType::STARTS_WITH:
					$captionQuery['nested']['query']['bool'][$boolOperator][] = array(
						'prefix' => array(
							'metadata.value_text.raw' => strtolower($metadataESearchItem->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::DOESNT_CONTAIN:
					$captionQuery['nested']['query']['bool'][$boolOperator][] = array(
						'term' => array(
							'metadata.value_text.raw' => strtolower($metadataESearchItem->getSearchTerm())
						)
					);
			}
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
			}
		}
		return $metadataQuery;
	}


}