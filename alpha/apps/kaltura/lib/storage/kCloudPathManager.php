<?php
/**
 * @package Core
 * @subpackage storage
 */
class kCloudPathManager extends kPathManager
{
	const CONTENT_FOLDER = 'content/';

	/**
	 *  will return a pair of file_root and file_path
	 *  This is the only function that should be extended for building a different path
	 */
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null, $storageProfileId = null)
	{
		$root = '/';
		$fileName = $object->generateFileName($subType, $version);
		$path = self::CONTENT_FOLDER . substr($fileName, 2, 2). '/' . substr($fileName, 4, 2) . '/' . $fileName;
		$path = str_replace('//', '/', $path);
		// need to check that file dont already exist in cloud

		KalturaLog::debug("Path [{$root}{$path}]");
		return array ($root, $path);
	}

}