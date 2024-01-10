<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kImportJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $srcFileUrl;
	
	/**
	 * @var string
	 */
	private $destFileLocalPath;
	
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var int
	 */
	private $fileSize;
	
	/**
	 * @var bool
	 */
	private $cacheOnly = false;
	
	/**
	 * @var string
	 */
	private $destFileSharedPath;

        /**
         * @var array
         */
	private $urlHeaders;

        /**
         * @var bool
     	 */
    	private $shouldRedirect;

    	/**
     	 * @return bool
     	 */
    	public function getShouldRedirect()
    	{
        	return $this->shouldRedirect;
    	}

    	/**
     	 * @param bool $shouldRedirect
     	 */
    	public function setShouldRedirect($shouldRedirect)
    	{
        	$this->shouldRedirect = $shouldRedirect;
    	}

	/**
	 * @return the $srcFileUrl
	 */
	public function getSrcFileUrl()
	{
		return $this->srcFileUrl;
	}
	
	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}
	
	/**
	 * @param $cacheOnly the $cacheOnly to set
	 */
	public function setCacheOnly($cacheOnly)
	{
		$this->cacheOnly = $cacheOnly;
	}

	/**
	 * @return the $cacheOnly
	 */
	public function getCacheOnly()
	{
		return $this->cacheOnly;
	}

	/**
	 * @return the $destFileLocalPath
	 */
	public function getDestFileLocalPath()
	{
		return $this->destFileLocalPath;
	}

	/**
	 * @param $srcFileUrl the $srcFileUrl to set
	 */
	public function setSrcFileUrl($srcFileUrl)
	{
		$this->srcFileUrl = $srcFileUrl;
	}

	/**
	 * @param $destFileLocalPath the $destFileLocalPath to set
	 */
	public function setDestFileLocalPath($destFileLocalPath)
	{
		$this->destFileLocalPath = $destFileLocalPath;
	}
	
	/**
	 * @return the $fileSize
	 */
	public function getFileSize() {
		return $this->fileSize;
	}
	
	/**
	 * @param number $fileSize
	 */
	public function setFileSize($fileSize) {
		$this->fileSize = $fileSize;
	}
	
	/**
	 * @return string
	 */
	public function getDestFileSharedPath()
	{
		return $this->destFileSharedPath;
	}
	
	/**
	 * @param string $destFileSharedPath
	 */
	public function setDestFileSharedPath($destFileSharedPath)
	{
		$this->destFileSharedPath = $destFileSharedPath;
	}
	
	/**
	 * @return array
	 */
	public function getUrlHeaders()
	{
		return $this->urlHeaders;
	}
	
	/**
	 * @param array $urlHeaders
	 */
	public function setUrlHeaders($urlHeaders)
	{
		$this->urlHeaders = $urlHeaders;
	}
}
