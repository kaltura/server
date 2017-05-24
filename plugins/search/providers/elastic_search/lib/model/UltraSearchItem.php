<?php

abstract class UltraSearchItem extends BaseObject
{

	/**
	 * @var UltraSearchItemType
	 */
	protected $itemType;

	/**
	 * @return UltraSearchItemType
	 */
	public function getItemType()
	{
		return $this->itemType;
	}

	/**
	 * @param UltraSearchItemType $itemType
	 */
	public function setItemType($itemType)
	{
		$this->itemType = $itemType;
	}

	/**
	 * @return null|string
	 */
	public function getQueryVerb()
	{
		$queryVerb = null;
		switch ($this->getItemType())
		{
			case UltraSearchItemType::EXACT_MATCH:
				$queryVerb = 'must';
				break;
			case UltraSearchItemType::PARTIAL:
				$queryVerb = 'query';
				break;
			case UltraSearchItemType::STARTS_WITH:
				$queryVerb = 'prefix';
				break;
			case UltraSearchItemType::DOESNT_CONTAIN:
				$queryVerb = 'must_not';
				break;
		}
		return $queryVerb;
	}

	/**
	 * In order to implement visitor/visited design pattern.
	 */
	abstract public function createSearchQuery();

}