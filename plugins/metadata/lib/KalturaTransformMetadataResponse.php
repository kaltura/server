<?php
class KalturaTransformMetadataResponse extends KalturaObject
{
	/**
	 * @var KalturaMetadataArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;

	/**
	 * @var int
	 * @readonly
	 */
	public $lowerVersionCount;
}