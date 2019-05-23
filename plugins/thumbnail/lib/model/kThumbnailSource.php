<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class kThumbnailSource
{
	public abstract function getImage();

	public function getLastModified()
	{
		return null;
	}
}