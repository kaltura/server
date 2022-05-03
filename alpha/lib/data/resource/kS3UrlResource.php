<?php
/**
 * Used to ingest media that is available on remote s3 object
 *
 * @package Core
 * @subpackage model.data
 */
class kS3UrlResource extends kUrlResource
{
	public function getType()
	{
		return 'kUrlResource';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see kUrlResource::forceAsyncDownload()
	 */
	public function getForceAsyncDownload()
	{
		return true;
	}
}