<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExportToCsvOptions extends KalturaObject
{
	/**
	 * The format of the outputted date string.
	 *
	 * @var string
	 */
	public $format;

	private static $mapBetweenObjects = array
	(
		'format',
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
			$dbAdditionalField = new kFormatField();

		return parent::toObject($dbAdditionalField, $skip);
	}
}
