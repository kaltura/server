<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, the media file won’t be downloaded but a file sync object of URL type will point to the media URL.
 *
 * @package Core
 * @subpackage model.data
 */
class kRemoteStorageResources extends kUrlResource implements IRemoteStorageResource
{
	/**
	 * Array of remote stoage resources 
	 * @var array<kRemoteStorageResource>
	 */
	private $resources;
	
	/* (non-PHPdoc)
	 * @see IRemoteStorageResource::getResources()
	*/
	public function getResources()
	{
		return $this->resources;
	}

	/**
	 * @param array<kRemoteStorageResource> $resources
	 */
	public function setResources(array $resources)
	{
		$this->resources = $resources;
	}

	/**
	 * @return string
	 */
	public function getFileExt()  
	{
		if (!count($this->resources))
			return null;
			
		return reset($this->resources)->getFileExt();
	}
}