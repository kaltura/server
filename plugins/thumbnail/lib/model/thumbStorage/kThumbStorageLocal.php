<?php
/**
 * @package plugins.thumbnail
<<<<<<< HEAD
 * @subpackage model
=======
 * @subpackage model.thumbStorage
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
 */

class kThumbStorageLocal extends kThumbStorageBase implements kThumbStorageInterface
{
<<<<<<< HEAD
	public function saveFile($fileName, $content)
	{
		$path = $this->getFullPath($fileName);
		kFile::fullMkdir($path);
		$ret = kFile::safeFilePutContents($path, $content);
=======
	public function saveFile($url, $content)
	{
		KalturaLog::debug("Saving file to:" . $url);
		$path = $this->getFullPath($url);
		kFile::fullMkdir($path);
		$ret = kFile::safeFilePutContents($path, $content);
		if(!$ret)
		{
			KalturaLog::err("Failed to save thumbnail file");
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}

>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		$this->fileName = $path;
		return $ret;
	}

<<<<<<< HEAD
	protected function getRenderer()
	{
		return kFileUtils::getDumpFileRenderer($this->fileName,self::MIME_TYPE,self::MAX_AGE);
	}

	public function loadFile($url)
	{
		$path = $this->getFullPath($url);
		$fileData = kFile::getFileData($path);
		$this->fileName = $path;
		return $fileData->exists;
=======
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

	public function deleteFile($url)
	{
		KalturaLog::debug("deleting file to:" . $url);
		$path = $this->getFullPath($url);
		kFile::deleteFile($path);
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	}
}