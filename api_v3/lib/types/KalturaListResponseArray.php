<?php
/**
 * Associative array of KalturaListResponse
 * 
 * @package api
 * @subpackage objects
 */
class KalturaListResponseArray extends KalturaAssociativeArray
{
	public function __construct()
	{
		return parent::__construct("KalturaListResponse");
	}
}
