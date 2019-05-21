<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class thumbnailSource
{
	public abstract function getImage();

	public function getLastModified()
	{
		return null;
	}
}