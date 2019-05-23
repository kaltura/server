<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

class kThumbStorageLocal extends kThumbStorageBase implements kThumbStorageInterface
{
	public function saveFile($fileName, $content)
	{
		KalturaLog::debug("Saving file to:" . $fileName);
		$path = $this->getFullPath($fileName);
		kFile::fullMkdir($path);
		$ret = kFile::safeFilePutContents($path, $content);
		if(!$ret)
		{
			KalturaLog::err("Failed to save thumbnail file");
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}

		$this->fileName = $path;
		return $ret;
	}

	protected function getRenderer($lastModified = null)
	{
		return kFileUtils::getDumpFileRenderer($this->fileName, self::MIME_TYPE, self::MAX_AGE, 0, $lastModified);
	}

	public function loadFile($url, $lastModified  = null)
	{
		KalturaLog::debug("loading file from path:" . $url);
		$path = $this->getFullPath($url);
		$fileData = kFile::getFileData($path);
		if(!$fileData->exists)
		{
			KalturaLog::debug("file wasn't found" . $url);
			return false;
		}

		if($lastModified && $fileData->last_modified < $lastModified)
		{
			KalturaLog::debug("file was created before entry changed" . $fileData->last_modified);
			return false;
		}

		$this->fileName = $path;
		return true;
	}
}