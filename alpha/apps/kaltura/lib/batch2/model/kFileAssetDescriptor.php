<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kFileAssetDescriptor 
{
	/**
	 * @var string
	 */
	private $fileSyncLocalPath;

	/**
	 * @var string
	 */
	private $fileSyncRemoteUrl;
	
	/**
	 * 
	 * @var string
	 */
	private $fileExt;
	
	/**
	 * 
	 * @var string
	 */
	private $name;
	
	/**
	 * @return the $fileSyncLocalPath
	 */
	public function getFileSyncLocalPath() {
		return $this->fileSyncLocalPath;
	}

	/**
	 * @return the $fileSyncRemoteUrl
	 */
	public function getFileSyncRemoteUrl() {
		return $this->fileSyncRemoteUrl;
	}

	/**
	 * @return the $fileExt
	 */
	public function getFileExt() {
		return $this->fileExt;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $fileSyncLocalPath
	 */
	public function setFileSyncLocalPath($fileSyncLocalPath) {
		$this->fileSyncLocalPath = $fileSyncLocalPath;
	}

	/**
	 * @param string $fileSyncRemoteUrl
	 */
	public function setFileSyncRemoteUrl($fileSyncRemoteUrl) {
		$this->fileSyncRemoteUrl = $fileSyncRemoteUrl;
	}

	/**
	 * @param string $fileExt
	 */
	public function setFileExt($fileExt) {
		$this->fileExt = $fileExt;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
}