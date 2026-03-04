<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaBulkRestoreEntryData extends KalturaObject
{
	/**
	 * Partner ID for the entries to restore
	 *
	 * @var int
	 */
	public $partnerId;

	/**
	 * Entry IDs to restore (comma-separated or newline-separated)
	 *
	 * @var string
	 */
	public $entryIds;

	/**
	 * Whether to perform a dry run (validation only)
	 *
	 * @var bool
	 */
	public $dryRun;

	private static $map_between_objects = array
	(
		'partnerId',
		'entryIds',
		'dryRun',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}