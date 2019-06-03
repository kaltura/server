<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

class kThumbStorageNone extends kThumbStorageBase implements kThumbStorageInterface
{
	public function saveFile($url, $content)
	{
		$this->url = $url;
		$this->content = $content;
	}

	protected function getRenderer($lastModified = null)
	{
		$renderer = new kRendererString($this->content,self::MIME_TYPE, self::MAX_AGE, $lastModified);
		return $renderer;
	}

	public function loadFile($url, $lastModified  = null)
	{
		return false;
	}

	public function deleteFile($url)
	{
		return false;
	}
}