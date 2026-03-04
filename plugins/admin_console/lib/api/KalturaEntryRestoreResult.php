<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaEntryRestoreResult extends KalturaObject
{
	/**
	 * The entry ID
	 *
	 * @var string
	 */
	public $entryId;

	/**
	 * Whether the entry was successfully restored
	 *
	 * @var bool
	 */
	public $restored;

	/**
	 * Error message if restoration failed
	 *
	 * @var string
	 */
	public $error;

	private static $map_between_objects = array
	(
		'entryId',
		'restored',
		'error',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}