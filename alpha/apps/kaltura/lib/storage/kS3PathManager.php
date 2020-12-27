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
		$filePath = kFile::fixPath($filePath);
		$root = $this->getRootPath($object, $storageProfileId);

		KalturaLog::debug("S3 Path [{$root}{$filePath}]");
		return array($root, $filePath);
	}

	protected function getRootPath(ISyncableFile $object, $storageProfileId = null)
	{
		$root = '/';
		$partnerId = $object->getPartnerId();
		
		if(!$storageProfileId && $partnerId)
		{
			$partner = PartnerPeer::retrieveByPK($partnerId);
			$storageProfileId = $partner ? $partner->getSharedStorageProfileId() : null;
		}
		
		if(!$storageProfileId)
		{
			return $root;
		}
		
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if($storageProfile && $storageProfile->getStorageBaseDir())
		{
			$root = $storageProfile->getStorageBaseDir();
		}
		
		return $root;
	}
}