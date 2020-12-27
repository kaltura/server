<?php

/**
 * Skeleton subclass for representing a row from the 'file_asset' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class FileAsset extends BaseFileAsset implements ISyncableFile, IRelatedObject
{
	const FILE_SYNC_ASSET = 1;
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(self::FILE_SYNC_ASSET);
		
		if(!in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::FILE_ASSET, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::FILE_ASSET;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null, $externalPath = false )
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();

		if($externalPath)
		{
			$path = '/fileAsset/';
			$dir = myContentStorage::getScatteredPathFromIntId($this->getId());
		}
		else
		{
			$path = '/content/fileAsset/';
			$dir = myContentStorage::getPathFromIntId($this->getId());
		}
		
		$path .= $dir . '/' . $this->generateFileName($sub_type, $version);
		return array(myContentStorage::getFSContentRootPath(), $path);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
		
		$fileExt = $this->getFileExt();
		if(!$fileExt)
			$fileExt = 'dat';
		
		return kMetadataManager::getObjectTypeName($this->getObjectType()) . "_" . $this->getObjectId() . "_" . $this->getId() . "_{$version}.{$fileExt}";
	}
	
	/**
	 * @var FileSync
	 */
	private $m_file_sync;
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		return $this->m_file_sync;
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $file_sync)
	{
		$this->m_file_sync = $file_sync;
	}

	public function incrementVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getVersion(), FileSyncObjectType::FILE_ASSET, self::FILE_SYNC_ASSET);
									
		$this->setVersion($newVersion);
	}

	/**
	 * @param string $entryId
	 * @return FileAsset The copied fileAsset
	 */
	public function copyToEntry($entryId)
	{
		$fileAssetCopy = $this->copy();
		$fileAssetCopy->setObjectId($entryId);
		$fileAssetCopy->save();
		return $fileAssetCopy;
	}

} // FileAsset
