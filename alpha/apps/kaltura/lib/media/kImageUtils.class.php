<?php

/**
 * @package infra
 * @subpackage Media
 */
class kImageUtils
{
	public static function getImageSize(FileSync $fileSync = null)
	{
		$width = $height = $type = $attr = null;
		
		if(!$fileSync)
		{
			return array($width, $height, $type, $attr);
		}
		
		if(!$fileSync->getEncryptionKey())
		{
			$key = kFileSyncUtils::getKeyForFileSync($fileSync);
			$filePath = kFile::realPath(kFileSyncUtils::getLocalFilePathForKey($key));
			kSharedFileSystemMgr::restoreStreamWrappers();
			list($width, $height, $type, $attr) = getimagesize($filePath);
			kSharedFileSystemMgr::unRegisterStreamWrappers();
		}
		else
		{
			$filePath = $fileSync->createTempClear();
			list($width, $height, $type, $attr) = getimagesize($filePath);
			$fileSync->deleteTempClear();
		}
		
		return array($width, $height, $type, $attr);
	}
}
