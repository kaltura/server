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


	abstract public function getSearchQuery();

}