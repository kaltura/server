<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFileContainer extends KalturaObject
{
	/**
	 * @var string
	 */
	public $filePath;

	/**
	 * @var string
	 */
	public $encryptionKey;

	/**
	 * @var int
	 */
	public $fileSize;

	private static $map_between_objects = array
	(
		"filePath",
		"encryptionKey",
		"fileSize",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/* (non-PHPdoc)
 * @see KalturaObject::toObject()
 */
	public function toObject ($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new FileContainer();
		return parent::toObject($dbObject, $skip);
	}

}
?>