<?php
/**
 * Object which contains contextual entry-related data.
 * @package api
 * @subpackage objects
 */
class KalturaPlaybackContextOptions extends KalturaEntryContextDataParams
{
	/**
	 * @var int
	 */
	public $clipFrom;

	/**
	 * @var int
	 */
	public $clipTo;
}