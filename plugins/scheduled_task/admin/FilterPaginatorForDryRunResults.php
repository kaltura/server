<?php
/**
 * @package Admin
 * @subpackage paginator
 */
class Kaltura_FilterPaginatorForDryRunResult implements Zend_Paginator_Adapter_Interface
{
	/**
	 * @var array
	 */
	protected $objects;


	/**
	 * @var int
	 */
	protected $count;

	
	public function __construct($dryRunId)
	{
		$results = MediaRepurposingUtils::getDryRunResult($dryRunId);
		$this->count = $results->totalCount;
		$this->objects = $results->objects;
	}


	public function getItems($offset, $itemCountPerPage) {
		$pageItems = array_slice($this->objects, $offset, $itemCountPerPage);
		return $pageItems;
	}

	public function count() {
		return $this->count;
	}



}