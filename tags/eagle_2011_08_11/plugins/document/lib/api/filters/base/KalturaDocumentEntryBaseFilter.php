<?php
/**
 * @package plugins.document
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaDocumentEntryBaseFilter extends KalturaBaseEntryFilter
{
	private $map_between_objects = array
	(
		"documentTypeEqual" => "_eq_document_type",
		"documentTypeIn" => "_in_document_type",
	);

	private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * 
	 * 
	 * @var KalturaDocumentType
	 */
	public $documentTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $documentTypeIn;
}
