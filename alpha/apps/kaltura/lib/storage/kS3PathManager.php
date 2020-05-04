<?php
/**
 * @package Core
 * @subpackage storage
 */
class kS3PathManager extends kPathManager
{
	/**
	 * will return a pair of file_root and file_path
	 * This is the only function that should be extended for building a different path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param $version
	 */
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null, $storageProfileId = null)
	{
		list($root, $filePath) = $object->generateFilePathArr($subType, $version, true);
		$filePath = str_replace('/content/', '/', $filePath);
		$filePath = str_replace('//', '/', $filePath);
		$path = '{partnerId}';

		if (method_exists($object, 'getEntryId') && $object->getEntryId())
		{
			$filePath = str_replace('/entry/', '/', $filePath);
			$path .= '/entry/{objectType}/{entryPath}/{filePath}';
			$path = str_replace('{objectType}', get_class($object), $path);
			$entryPath = substr($object->getEntryId(), -3) . '/' . $object->getEntryId();
			$path = str_replace('{entryPath}', $entryPath, $path);
		}
		else
		{
			$path .= '/{filePath}';
		}

		$path = str_replace('{filePath}', $filePath, $path);
		$path = str_replace('{partnerId}', $object->getPartnerId(), $path);
		$path = strtolower($path);
		$root = '/';

		KalturaLog::debug("S3 Path [{$root}{$path}]");
		return array($root, $path);
	}
}