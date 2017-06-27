<?php

class ESearchCaptionItem extends ESearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var int;
	 */
	protected $startTimeInVideo;

	/**
	 * @var int;
	 */
	protected $endTimeInVideo;


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
		return 'caption';
	}

	/**
	 * @return int
	 */
	public function getStartTimeInVideo()
	{
		return $this->startTimeInVideo;
	}

	/**
	 * @param int $startTimeInVideo
	 */
	public function setStartTimeInVideo($startTimeInVideo)
	{
		$this->startTimeInVideo = $startTimeInVideo;
	}

	/**
	 * @return int
	 */
	public function getEndTimeInVideo()
	{
		return $this->endTimeInVideo;
	}

	/**
	 * @param int $endTimeInVideo
	 */
	public function setEndTimeInVideo($endTimeInVideo)
	{
		$this->endTimeInVideo = $endTimeInVideo;
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $additionalParams = null)
	{
		$captionQuery['nested']['path'] = 'caption_assets';
		$captionQuery['nested']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
		$captionQuery['nested']['inner_hits'] = array('size' => 10, '_source' => false);
		$captionQuery['nested']['query']['nested']['path'] = "caption_assets.lines";
		foreach ($eSearchItemsArr as $eSearchCaptionItem)
		{
			/* @var ESearchCaptionItem $eSearchCaptionItem */
			switch ($eSearchCaptionItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array(
						'term' => array(
							'caption_assets.lines.content.raw' => strtolower($eSearchCaptionItem->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::PARTIAL:
					$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array(
						'multi_match' => array(
							'query' => strtolower($eSearchCaptionItem->getSearchTerm()),
							'fields' => array(
								'caption_assets.lines.content.trigrams',
								'caption_assets.lines.content.raw^3',
								'caption_assets.lines.content^2',
								'caption_assets.lines.content_*^2',
							),
							'type' => 'most_fields'
						)
					);
					break;
				case ESearchItemType::STARTS_WITH:
					$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array(
						'prefix' => array(
							'caption_assets.lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
						)
					);
					break;
				case ESearchItemType::DOESNT_CONTAIN:
					$captionQuery['has_child']['query']['nested']['query']['bool']['must_not'][] = array(
						'term' => array(
							'caption_assets.lines.content' => strtolower($eSearchCaptionItem->getSearchTerm())
						)
					);
					break;
			}

			foreach ($eSearchCaptionItem->getRanges() as $range)
			{
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('caption_assets.lines.start_time' => array('lte' => $range[0])));
				$captionQuery['nested']['query']['nested']['query']['bool'][$boolOperator][] = array('range' => array('caption_assets.lines.end_time' => array('gte' => $range[1])));
			}
		}
		foreach ($additionalParams as $addParamKey => $addParamVal)
		{
			$captionQuery['has_child']['query']['nested']['query']['bool'][$addParamKey] = $addParamVal;
		}
		return $captionQuery;
	}

}