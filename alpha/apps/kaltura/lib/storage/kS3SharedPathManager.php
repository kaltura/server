<?php
/**
 * @package Core
 * @subpackage storage
 */
class kS3SharedPathManager extends kPathManager
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
		$sharedPath = '/entry/' . get_class($object) . '/' .
			substr($object->getEntryId(), -2) . '/' .
			substr($object->getEntryId(), -4, 2) . '/' .
			$object->generateFileName($subType, $version);
		
		return array(kSharedFileSystemMgr::getSharedRootByType(kSharedFileSystemMgrType::S3), $sharedPath);
	}
}