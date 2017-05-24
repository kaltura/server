<?php

abstract class EnhancedSearchItem extends BaseObject
{

	/**
	 * @var EnhancedSearchItemType
	 */
	protected $itemType;

	/**
	 * @return EnhancedSearchItemType
	 */
	public function getItemType()
	{
		return $this->itemType;
	}

	/**
	 * @param EnhancedSearchItemType $itemType
	 */
	public function setItemType($itemType)
	{
		$this->itemType = $itemType;
	}

	/**
	 * @return null|string
	 */
	protected function getQueryVerb()
	{
		$queryVerb = null;
		switch ($this->getItemType())
		{
			case EnhancedSearchItemType::EXACT_MATCH:
				$queryVerb = 'must';
				break;
			case EnhancedSearchItemType::PARTIAL:
				$queryVerb = 'query';
				break;
			case EnhancedSearchItemType::STARTS_WITH:
				$queryVerb = 'prefix';
				break;
			case EnhancedSearchItemType::DOESNT_CONTAIN:
				$queryVerb = 'must_not';
				break;
		}
		return $queryVerb;
	}

	abstract public function getSearchQuery();

}