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

	public function toObject ()
	{
		$src = new FileContainer();
		return parent::toObject($src);
	}

}
?>