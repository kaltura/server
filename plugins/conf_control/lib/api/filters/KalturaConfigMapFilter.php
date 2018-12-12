<?php
/**
 * @package plugins.confControl
 * @subpackage api.filters
 */
class KalturaConfigMapFilter extends KalturaFilter
{
	/**
	 * Name of the map
	 *
	 * @var string
	 * @insertonly
	 */
	public $name;

	/**
	 * Regex that represent the host/s that this map affect
	 *
	 * @var string
	 */
	public $relatedHost;


	public function getCoreFilter()
	{
		return null;
	}
}
