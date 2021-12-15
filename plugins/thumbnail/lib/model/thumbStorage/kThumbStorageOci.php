<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */
use Oracle\Oci\ObjectStorage\StreamWrapper;

class kThumbStorageOci extends kThumbStorageBase implements kThumbStorageInterface
{
	/** @var kOciSharedFileSystemMgr $osClient */
	protected $osClient;
	
	function __construct()
	{
		$osClient = kSharedFileSystemMgr::getInstance(StorageProfileProtocol::OCI);
		$this->osClient = $osClient->getOsClient();
		
		StreamWrapper::register($this->osClient, array(
			'oci' => array(
				StreamWrapper::NAMESPACE_NAME_PARAM => $this->osClient->getNamespace(),
				StreamWrapper::REGION_PARAM => $this->osClient->getRegion()
			)
		));
	}
	
	protected function getRenderer($type = self::DEFAULT_MIME_TYPE, $lastModified = null)
	{
		$renderer = new kRendererString($this->content, $type, $lastModified);
		return $renderer;
	}
	
	public function saveFile($fileName, $content)
	{
		$path = $this->getFullPath($fileName);
		$this->url = self::getUrl($path);
		if(kFile::filePutContents($this->url, $content))
		{
			$this->content = $content;
		}
		else
		{
			KalturaLog::err('Failed to save thumbnail file');
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}
	}
	
	public function loadFile($url, $lastModified)
	{
		KalturaLog::debug('Loading file from OS ' . $url);
		$path = $this->getFullPath($url);
		$this->url = self::getUrl($path);
		try
		{
			if (file_exists($this->url)) // TODO should I use kFile::checkFileExists?
			{
				if ($lastModified)
				{
					$osLastModified = filemtime($this->url); // TODO should I use kFile::filemtime()?
					if ($lastModified > $osLastModified)
					{
						KalturaLog::debug('File was created before entry changed ' . $osLastModified);
						return false;
					}
				}
				
				// TODO when testing, double check it's matching the 'kFileTransferMgr::getFile($path)'
				$path = kFile::fixPath($path);
				$this->content = kFile::getFileContent($path);
				return true;
			}
		}
		catch (Exception $e)
		{
			KalturaLog::debug('Failed to load file ' . $e->getMessage());
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}
		
		return false;
	}
	
	public function deleteFile($url)
	{
		KalturaLog::debug('Deleting file from OS: ' . $url);
		return kFile::deleteFile($url);
	}
	
	public static function getUrl($path)
	{
		return 'oci://' . $path;
	}
	
	public function getType()
	{
		$image = new Imagick();
		$image->readImageBlob($this->content);
		$imageFormat = $image->getImageFormat();
		if ($imageFormat)
		{
			return 'image/' . strtolower($imageFormat);
		}
		
		return parent::getType();
	}
}