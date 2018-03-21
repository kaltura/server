<?php
/**
* @package plugins.konference
* @subpackage api.objects
*/
class KalturaRoomDetails extends KalturaObject
{
	/**
	 * @var string
	 */
	public $serverUrl;

	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var string
	 */
	public $token;

	private static $map_between_objects = array
	(
	);
	
}