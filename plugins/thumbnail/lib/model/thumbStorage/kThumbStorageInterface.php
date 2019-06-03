<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

interface kThumbStorageInterface
{
	public function saveFile($url,$content);
	public function loadFile($path);
}