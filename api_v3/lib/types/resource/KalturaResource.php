<?php
/**
 * Used to ingest entry object, as single resource or list of resources accompanied by asset params ids.
 * 
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaResource extends KalturaObject 
{
	public function validateEntry(entry $dbEntry)
	{
		
	}
	
	public function entryHandled(entry $dbEntry)
	{
		
	}

	public function improveResource()
	{

	}
}