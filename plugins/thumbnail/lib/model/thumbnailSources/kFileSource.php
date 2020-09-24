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
		$this->imagick = new Imagick($filePath);
	}

	public function getImage()
	{
		return $this->imagick;
	}
}