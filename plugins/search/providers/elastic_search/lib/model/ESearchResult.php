<?php

class ESearchResult extends BaseObject
{

	/**
	 * @var Baseentry
	 */
	protected $entry;

	/**
	 * @var string
	 */
	protected $itemData;

	/**
	 * @return Baseentry
	 */
	public function getEntry()
	{
		return $this->entry;
	}

	/**
	 * @param Baseentry $entry
	 */
	public function setEntry($entry)
	{
		$this->entry = $entry;
	}

	/**
	 * @return string
	 */
	public function getItemData()
	{
		return $this->itemData;
	}

	/**
	 * @param string $itemData
	 */
	public function setItemData($itemData)
	{
		$this->itemData = $itemData;
	}


}