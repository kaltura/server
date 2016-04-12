<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage api.objects
 */
class KalturaTvinciDistributionTag extends KalturaObject
{
	/**
	 * @var string
	 */
	public $tagname;

	/**
	 * @var string
	 */
	public $extension;

	/**
	 * @var string
	 */
	public $protocol;

	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var string
	 */
	public $ppvmodule;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	 */
	private static $map_between_objects = array 
	(
		'tagname',
		'extension',
		'protocol',
		'format',
		'filename',
		'ppvmodule',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new TvinciDistributionTag();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}