<?php
/**
 * Is a unified way to add content to Kaltura whether it's an uploaded file, webcam recording, imported URL or existing file sync.
 * 
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaContentResource extends KalturaResource 
{
	public function validateAsset(asset $dbAsset)
	{
	
	}
}