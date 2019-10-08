<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbnailSources
 */

class kFileSource extends kThumbnailSource
{
	protected $imagick;

	public function  __construct($filePath)
	{
		$this->imagick = new Imagick($filePath);
	}

	public function getImage()
	{
		return $this->imagick;
	}
}