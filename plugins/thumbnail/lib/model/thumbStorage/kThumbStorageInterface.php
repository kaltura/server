<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

interface kThumbStorageInterface
{
	public function saveFile($url,$content);
	public function loadFile($path);
}