<?php
/**
 * @package plugins.venodr
 * @subpackage zoom.data
 */
class kZoomRecordingFile implements iZoomObject
{
	const FILE_TYPE = 'file_type';
	const DOWNLOAD_URL = 'download_url';

	public $fileType;
	public $download_url;

	public function parseData($data)
	{
		$this->fileType = $data[self::FILE_TYPE];
		$this->download_url = $data[self::DOWNLOAD_URL];
	}
}
