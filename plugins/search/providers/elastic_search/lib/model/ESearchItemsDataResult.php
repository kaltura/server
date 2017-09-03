<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchItemsDataResult extends BaseObject
{
	/**
	 * @var int
	 */
	protected $totalCount;

	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @var string
	 */
	protected $itemsType;

	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->totalCount;
	}

	/**
	 * @param int $totalCount
	 */
	public function setTotalCount($totalCount)
	{
		$this->totalCount = $totalCount;
	}

	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param array $items
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}

	/**
	 * @return string
	 */
	public function getItemsType()
	{
		return $this->itemsType;
	}

	/**
	 * @param string $itemsType
	 */
	public function setItemsType($itemsType)
	{
		$this->itemsType = $itemsType;
	}

}
