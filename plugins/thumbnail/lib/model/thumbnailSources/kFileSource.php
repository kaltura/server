<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbnailSources
 */

class kFileSource extends kThumbnailSource
{
	protected $imagick;

	/**
	 * kFileSource constructor.
	 * @param $filePath
	 * @throws ImagickException
	 */
	public function __construct($filePath)
	{
		$filePath = kFile::realPath($filePath);
		stream_wrapper_restore('https');
		$this->imagick = new Imagick($filePath);
		stream_wrapper_unregister('https');
	}

	public function getImage()
	{
		return $this->imagick;
	}
}