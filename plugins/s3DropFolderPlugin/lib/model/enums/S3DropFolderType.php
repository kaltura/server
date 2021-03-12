<?php
/**
 * @package plugins.s3DropFolder
 *  @subpackage model.enum
 */
class S3DropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const S3DROPFOLDER = 'S3DROPFOLDER';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array('S3DROPFOLDER' => self::S3DROPFOLDER);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
