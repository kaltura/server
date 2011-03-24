<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different flavor asset in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync of the existing flavor asset.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaAssetResource extends KalturaContentResource 
{
	/**
	 * ID of the source asset 
	 * @var string
	 */
	public $assetId;
}