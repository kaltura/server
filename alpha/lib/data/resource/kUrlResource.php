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
}