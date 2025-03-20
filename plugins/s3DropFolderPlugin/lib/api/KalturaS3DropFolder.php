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
	
	/**
	 * @var bool
	 */
	public $useS3Arn;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $s3Arn;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		's3Host',
		's3Region',
		's3UserId',
		's3Password',
		'useS3Arn',
		's3Arn'
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
	
	public function validateForInsert($propertiesToSkip = array())
	{
		if ($this->useS3Arn)
		{
			if (empty(kConf::getArrayValue('s3Arn', 's3_drop_folder', 'runtime_config', null)))
			{
				throw new KalturaAPIException(KalturaS3DropFolderErrors::MISSING_S3ARN_CONFIG);
			}
		}
		
		parent::validateForInsert($propertiesToSkip);
	}
}
