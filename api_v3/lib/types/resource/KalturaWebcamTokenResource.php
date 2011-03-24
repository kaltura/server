<?php
/**
 * Used to ingest media that streamed to the system and represented by token that returned from media server such as FMS or red5.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaWebcamTokenResource extends KalturaContentResource 
{
	/**
	 * Token that returned from media server such as FMS or red5. 
	 * @var string
	 */
	public $token;
}