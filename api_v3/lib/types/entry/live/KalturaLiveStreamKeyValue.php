<?php
/**
 * A key value pair representation to return an array of live stream key-value pairs (associative array)
 * 
 * @see KalturaLiveStreamKeyValueArray
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamKeyValue extends KalturaKeyValue
{
	/**
	 * Key - must be valid playback protocol
	 * @var KalturaPlaybackProtocol
	 */
	public $key;
	
}