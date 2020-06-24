<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

interface kThumbStorageInterface
{
	public function saveFile($url, $content);
	public function loadFile($url, $lastModified);
	public function deleteFile($url);
	public function getType();
}