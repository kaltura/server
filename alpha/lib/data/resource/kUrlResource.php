<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready.
 *
 * @package Core
 * @subpackage model.data
 */
class kUrlResource extends kContentResource 
{
	/**
	 * Remote URL, FTP, HTTP or HTTPS 
	 * @var string
	 */
	private $url;
	
	/**
	 * Force the URL download as an asynchronous batch job (false will execute the download immediately upon attaching the resource to an entry or asset).
	 * @var bool
	 */
	private $forceAsyncDownload = false;
	
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
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	/**
	 * Return import job data to use to import the file from the url
	 * @return kImportJobData
	 */
	public function getImportJobData()
	{
		$importJobData = new kImportJobData();
		$importJobData->setUrlHeaders($this->getUrlHeaders());
        	$importJobData->setShouldRedirect($this->getShouldRedirect());
		return $importJobData;
	}
	
	/**
	 * @return bool
	 */
	public function getForceAsyncDownload()
	{
	    return $this->forceAsyncDownload;
	}
	
	public function setForceAsyncDownload($force)
	{
	    $this->forceAsyncDownload = $force;
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
