<?php
/**
 * Used to ingest media that is accessible using search media provider search results.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaSearchResultsResource extends KalturaContentResource 
{
	/**
	 * Search media provider search results 
	 * @var KalturaSearchResult
	 */
	public $result;
}