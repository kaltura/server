<?php
/**
 * @package Admin
 * @subpackage paginator
 */
class Kaltura_FilterPaginatorList implements Zend_Paginator_Adapter_Interface
{
	/**
	 * @var array
	 */
	protected $objects;


	/**
	 * @var int
	 */
	protected $count;

	
	public function __construct($objects)
	{
		$this->count = count($objects);
		$this->objects = $objects;
	}


	public function getItems($offset, $itemCountPerPage) {
		$pageItems = array_slice($this->objects, $offset, $itemCountPerPage);
		return $pageItems;
	}

	public function count() {
		return $this->count;
	}



}