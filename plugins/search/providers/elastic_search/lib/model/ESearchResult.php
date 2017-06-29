<?php

class ESearchResult extends BaseObject
{

	/**
	 * @var BaseObject
	 */
	protected $object;

	/**
	 * @var string
	 */
	protected $itemData;

	/**
	 * @return BaseObject
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @param BaseObject $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
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