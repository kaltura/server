<?php
/**
 * @package core
 * @subpackage thumbnail.thumbStorage
 */

class kThumbStorageLocal extends kThumbStorageBase implements kThumbStorageInterface
{
	public function saveFile($url, $content)
	{
		KalturaLog::debug('Saving file to:' . $url);
		$path = $this->getFullPath($url);
		kFile::fullMkdir($path);
		$ret = kFile::safeFilePutContents($path, $content);
		if(!$ret)
		{
			KalturaLog::err('Failed to save thumbnail file');
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}

		$this->fileName = $path;
		return $ret;
	}

	protected function getRenderer($type = self::DEFAULT_MIME_TYPE, $lastModified = null)
	{
		return kFileUtils::getDumpFileRenderer($this->fileName, $type, self::MAX_AGE, 0, $lastModified);
	}

	public function loadFile($url, $lastModified  = null)
	{
		KalturaLog::debug('loading file from path:' . $url);
		$path = $this->getFullPath($url);
		$fileData = kFile::getFileData($path);
		if(!$fileData->exists)
		{
			KalturaLog::debug('file was not found' . $url);
			return false;
		}

		if($lastModified && $fileData->last_modified < $lastModified)
		{
			KalturaLog::debug('file was created before entry changed' . $fileData->last_modified);
			return false;
		}

		$this->fileName = $path;
		return true;
	}

	public function deleteFile($url)
	{
		KalturaLog::debug('deleting file to:' . $url);
		$path = $this->getFullPath($url);
		kFile::deleteFile($path);
	}

	public function getType()
	{
		$image = new Imagick();
		$image->readImage($this->fileName);
		$imageFormat = $image->GetImageFormat();
		if($imageFormat)
		{
			return 'image/' . strtolower($imageFormat);
		}

		return parent::getType();
	}
}