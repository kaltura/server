<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.objects
 */
class KalturaTagListResponse extends KalturaListResponse
{
    /**
	 * @var KalturaTagArray
	 * @readonly
	 */
	public $objects;

	
	public function __construct()
	{
	    $this->objects = array();
	    $this->totalCount = count($this->objects);
	}
}