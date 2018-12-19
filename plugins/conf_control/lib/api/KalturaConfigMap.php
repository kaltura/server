<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 12/11/2018
 * Time: 9:52 PM
 */

/**
 * @package plugins.confControl
 * @subpackage api.objects
 */

class KalturaConfigMap extends KalturaObject
{
	/**
	 * Name of the map
	 *
	 * @var string
	 * @insertonly
	 */
	public $name;

	/**
	 * Ini file content
	 *
	 * @var string
	 */
	public $content;

	/**
	 * IsEditable - true / false
	 *
	 * @var bool
	 * @readonly
	 */
	public $isEditable;

	/**
	 * Time of the last update
	 *
	 * @var time
	 * @readonly
	 */
	public $lastUpdate;

	/**
	 * Regex that represent the host/s that this map affect
	 *
	 * @var string
	 */
	public $relatedHost;


	/**
	 * @var int
	 * @readonly
	 */
	public $version;

	/**
	 * @var KalturaConfMapSourceLocation
	 * @insertonly
	 */
	public $sourceLocation;

	public function validateForUpdate($sourceObject)
	{
		
	}
}