
<?php
/**
 * @package plugins.S3DropFolder
 * @subpackage api.objects
 */
class KalturaS3DropFolder extends KalturaDropFolder
{
	/**
	 * @var string
	 */
	public $s3Host;

	/**
	 * @var string
	 */
	public $s3Region;

	/**
	 * @var string
	 */
	public $s3UserId;

	/**
	 * @var string
	 */
	public $s3Password;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		's3Host',
		's3Region',
		's3UserId',
		's3Password'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new S3DropFolder();
		}

		$dbObject->setType(S3DropFolderPlugin::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER));
		return parent::toObject($dbObject, $skip);
	}
}
