<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbnailSource
 */

abstract class kThumbnailSource
{
	public abstract function getImage();

	public function getLastModified()
	{
		return null;
	}
}