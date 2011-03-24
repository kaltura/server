<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready. The bulk upload id will be saved on the entry.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaBulkResource extends KalturaUrlResource 
{
	/**
	 * ID of the bulk upload job to be associated with the entry 
	 * @var string
	 */
	public $bulkUploadId;
}