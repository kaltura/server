<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExportToCsvOptions extends KalturaObject
{
	/**
	 * The format of the outputted date string. There are also several predefined date constants that may be used instead, so for example DATE_RSS contains the format string 'D, d M Y H:i:s'.
	 * https://www.php.net/manual/en/function.date.php
	 * @var string
	 */
	public $format;
	/**
	 * Additional columns will be added to the report that related to this specific object type(currently only MEDIA_CLIP),
	 * note that it will not filter the result and other objects will be printed also
	 * @var KalturaEntryType
	 */
	public $typeEqual;

	private static $mapBetweenObjects = array
	(
		'format',
		'typeEqual',
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
