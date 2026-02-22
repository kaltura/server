<?php
/**
 * @package UI-infra
 * @subpackage paginator
 */
class Infra_ArrayPaginator implements Zend_Paginator_Adapter_Interface
{
	/**
	 * @var array
	 */
	protected $items;

	/**
	 * Constructor.
	 *
	 * @param array $items The array of items to paginate
	 */
	public function __construct(array $items)
	{
		$this->items = $items;
	}

	/**
	 * Returns an array of items for a page.
	 *
	 * @param  integer $offset Page offset
	 * @param  integer $itemCountPerPage Number of items per page
	 * @return array
	 */
	public function getItems($offset, $itemCountPerPage)
	{
		return array_slice($this->items, $offset, $itemCountPerPage);
	}

	/**
	 * Returns the total number of items
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}
}