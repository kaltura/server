<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kThumbStorageNone extends kThumbStorageBase implements kThumbStorageInterface
{
	public function saveFile($url,$content)
	{
		$this->url = $url;
		$this->content = $content;
	}
	protected function getRenderer()
	{
		$renderer = new kRendererString($this->content,self::MIME_TYPE);
		return $renderer;
	}
	public function loadFile($path)
	{
		return false;
	}
}