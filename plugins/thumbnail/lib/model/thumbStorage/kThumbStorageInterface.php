<?php
/**
 * @package plugins.thumbnail
<<<<<<< HEAD
 * @subpackage model
=======
 * @subpackage model.thumbStorage
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
 */

interface kThumbStorageInterface
{
<<<<<<< HEAD
	public function saveFile($url,$content);
	public function loadFile($path);
=======
	public function saveFile($url, $content);
	public function loadFile($url, $lastModified);
	public function deleteFile($url);
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
}