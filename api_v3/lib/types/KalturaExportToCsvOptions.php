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
	 * Setting this property will cause additional columns to be added to the final report. The columns will be related to the specific object type passed (currently only MEDIA_CLIP is supported).
	 * Please note that this property will NOT change the result filter in any way (i.e passing MEDIA_CLIP here will not force the report to return only media items).
	 * @var KalturaEntryType
	 */
	public $typeEqual;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $defaultHeader;

	private static $mapBetweenObjects = array
	(
		'format',
		'typeEqual',
		'defaultHeader',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	/* (non-PHPdoc)
 	* @see KalturaObject::toObject()
 	*/
	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (is_null($objectToFill))
			$objectToFill = new kExportToCsvOptions();
		
		return parent::toObject($objectToFill, $propsToSkip);
	}
}
