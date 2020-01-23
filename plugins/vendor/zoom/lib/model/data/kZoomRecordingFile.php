<?php
/**
 * @package plugins.venodr
 * @subpackage zoom.data
 */
class kZoomRecordingFile implements iZoomObject
{
	const FILE_TYPE = 'file_type';
	const DOWNLOAD_URL = 'download_url';
	const ID = 'id';
	const NO_ID = 'noID';

	public $fileType;
	public $download_url;
	public $id;

	public function parseData($data)
	{
		$this->fileType = $data[self::FILE_TYPE];
		$this->download_url = $data[self::DOWNLOAD_URL];
		if(isset($data[self::ID]))
		{
			$this->id = $data[self::ID];
		}
		else
		{
			$this->id = self::NO_ID;
		}
	}
}
