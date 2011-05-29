<?php
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
			$format = '{defaultDateDir}/{partnerDir}/{filename}';
		}
		else
		{
			$format = '{pathFormat}/{filename}';
		}
		
		$fileName = $object->generateFileName($subType, $version);
		$partnerDir = floor($object->getPartnerId() / 1000);
		$defaultDateDir = date ("Ymd");
		
		$pathDir = str_replace ('Y', date ("Y"), $path_format);
		$pathDir = str_replace('m', date ("m"), $pathDir);
		$pathDir = str_replace('d', date ("d"), $pathDir);
		
		$path = str_replace('{filename}', $fileName, $format);
		$path = str_replace('{partnerDir}', $partnerDir, $path);
		$path = str_replace('{defaultDateDir}', $defaultDateDir, $path);
		$path = str_replace('{pathFormat}', $pathDir, $path);
		
		$root = '/';
		return array($root, $path);
	}
}