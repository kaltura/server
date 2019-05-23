<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

class kThumbStorageLocal extends kThumbStorageBase implements kThumbStorageInterface
{
	public function saveFile($fileName, $content)
	{
		$path = $this->getFullPath($fileName);
		kFile::fullMkdir($path);
		$ret = kFile::safeFilePutContents($path, $content);
		$this->fileName = $path;
		return $ret;
	}

	protected function getRenderer($lastModified = null)
	{
		return kFileUtils::getDumpFileRenderer($this->fileName, self::MIME_TYPE, self::MAX_AGE, 0, $lastModified);
	}

	public function loadFile($url)
	{
		$path = $this->getFullPath($url);
		$fileData = kFile::getFileData($path);
		$this->fileName = $path;
		return $fileData->exists;
	}
}