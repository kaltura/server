<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaUrlResource extends KalturaContentResource 
{
	/**
	 * Remote URL, FTP, HTTP or HTTPS 
	 * @var string
	 */
	public $url;
}