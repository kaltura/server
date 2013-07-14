<?php
/**
 * @package Core
 * @subpackage storage
 */
class kExternalPathManager extends kPathManager
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
		$storageProfile = kPathManager::getStorageProfile($storageProfileId);
		$path_format = $storageProfile->getPathFormat();
		
		if (is_null($path_format))
		{
			$path_format = '{year}{month}{day}/{partnerDir}/{fileName}';
		}
		
		$fileName = $object->generateFileName($subType, $version);
		$partnerDir = floor($object->getPartnerId() / 1000);
		
		$path = str_replace('{fileName}', $fileName, $path_format);
		$path = str_replace('{partnerDir}', $partnerDir, $path);
		$path = str_replace('{year}',  date("Y"), $path);
		$path = str_replace('{month}', date("m"), $path);
		$path = str_replace('{day}',   date("d"), $path);
		
		$root = '/';
		return array($root, $path);
	}
}