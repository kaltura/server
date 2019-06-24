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

	protected function getRenderer($type = self::DEFAULT_MIME_TYPE, $lastModified = null)
	{
		$renderer = new kRendererString($this->content, $type, self::MAX_AGE, $lastModified);
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

	public function getType()
	{
		$imageFormat = $this->content->GetImageFormat();
		if($imageFormat)
		{
			return 'image/' . strtolower($imageFormat);
		}

		return parent::getType();
	}
}