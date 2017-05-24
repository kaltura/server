<?php

class EnhancedSearchCaptionItem extends EnhancedSearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

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

	public function getSearchQuery()
	{
		$captionQuery = null;
		switch ($this->getItemType())
		{
			case EnhancedSearchItemType::EXACT_MATCH:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'term' => array(
						'lines.content' => strtolower($this->getSearchTerm())
					)
				);
				break;
			case EnhancedSearchItemType::PARTIAL:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'multi_match'=> array(
						'query'=> strtolower($this->getSearchTerm()),
						'fields'=> array(
							'lines.content',
							'lines.content_*' //todo change here if we want to choose the language to search
						),
						'type'=> 'most_fields'
					)
				);
				break;
			case EnhancedSearchItemType::STARTS_WITH:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must'][] = array(
					'prefix' => array(
						'lines.content' => strtolower($this->getSearchTerm())
					)
				);
				break;
			case EnhancedSearchItemType::DOESNT_CONTAIN:
				$captionQuery['has_child']['query']['nested']['query']['bool']['must_not'][] = array(
					'term' => array(
						'lines.content' => strtolower($this->getSearchTerm())
					)
				);
				break;
		}
		return $captionQuery;
	}


}