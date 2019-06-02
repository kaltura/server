<?php
/**
 * @package plugins.thumbnail
<<<<<<< HEAD
 * @subpackage model
=======
 * @subpackage model.thumbStorage
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
 */

class kThumbStorageNone extends kThumbStorageBase implements kThumbStorageInterface
{
<<<<<<< HEAD
	public function saveFile($url,$content)
=======
	public function saveFile($url, $content)
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	{
		$this->url = $url;
		$this->content = $content;
	}

<<<<<<< HEAD
	protected function getRenderer()
	{
		$renderer = new kRendererString($this->content,self::MIME_TYPE);
		return $renderer;
	}

	public function loadFile($path)
=======
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
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	{
		return false;
	}
}