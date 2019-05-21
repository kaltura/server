<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbnailSources
 */

class fileSource extends thumbnailSource
{
	protected $imagick;

	public function  __construct($filePath)
	{
		$this->imagick = new imagick($filePath);
	}

	public function getImage()
	{
		return $this->imagick;
	}
}