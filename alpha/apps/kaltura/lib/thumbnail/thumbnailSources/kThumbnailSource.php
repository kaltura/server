<?php
/**
 * @package core
 * @subpackage thumbnail.thumbnailSources
 */

abstract class kThumbnailSource
{

	public abstract function getImage();

	public function getLastModified()
	{
		return null;
	}
}