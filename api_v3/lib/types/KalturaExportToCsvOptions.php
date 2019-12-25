<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExportToCsvOptions extends KalturaObject
{
	/**
	 * The format of the outputted date string.
	 * @var KalturaExportToCsvOption // TODO: add it
	 */
	public $option;

	private static $mapBetweenObjects = array
	(
		'option',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	/* (non-PHPdoc)
 	* @see KalturaObject::toObject()
 	*/
	public function toObject($dbAdditionalField = null, $skip = array())
	{
		if(!$dbAdditionalField)
			$dbAdditionalField = new kExportToCsvOptions();

		return parent::toObject($dbAdditionalField, $skip);
	}
}
