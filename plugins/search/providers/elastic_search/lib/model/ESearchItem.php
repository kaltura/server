<?php

abstract class ESearchItem extends BaseObject
{

	/**
	 * @var ESearchItemType
	 */
	protected $itemType;

	/**
	 * @return ESearchItemType
	 */
	public function getItemType()
	{
		return $this->itemType;
	}

	/**
	 * @param ESearchItemType $itemType
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
			case ESearchItemType::EXACT_MATCH:
				$queryVerb = 'must';
				break;
			case ESearchItemType::PARTIAL:
				$queryVerb = 'query';
				break;
			case ESearchItemType::STARTS_WITH:
				$queryVerb = 'prefix';
				break;
			case ESearchItemType::DOESNT_CONTAIN:
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