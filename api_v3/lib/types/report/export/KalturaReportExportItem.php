<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportExportItem extends KalturaObject
{

	/**
	 * @var string
	 */
	public $reportTitle;

	/**
	 * @var KalturaReportExportItemType
	 */
	public $action;

	/**
	 * @var KalturaReportType
	 */
	public $reportType;

	/**
	 * @var KalturaReportInputFilter
	 */
	public $filter;

	/**
	 * @var string
	 */
	public $order;

	/**
	 * @var string
	 */
	public $objectIds;

	/**
	 * @var KalturaReportResponseOptions
	 */
	public $responseOptions;

	private static $map_between_objects = array
	(
		"reportTitle",
		"action",
		"reportType",
		"order",
		"objectIds",
		"responseOptions",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kReportExportItem();
		}
		$object_to_fill->setFilter($this->filter);

		return parent::toObject($object_to_fill, array('filter'));
	}

	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);
		$this->filter = $srcObj->getFilter();
	}

}
