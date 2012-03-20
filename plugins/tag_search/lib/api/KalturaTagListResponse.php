<?php
class KalturaTagListResponse extends KalturaObject
{
    /**
	 * @var KalturaTagArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
	
	public function __construct()
	{
	    $this->objects = array();
	    $this->totalCount = count($this->objects);
	}
}