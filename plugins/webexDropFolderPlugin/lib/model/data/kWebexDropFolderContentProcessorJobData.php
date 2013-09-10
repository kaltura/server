<?php
class kWebexDropFolderContentProcessorJobData extends kDropFolderContentProcessorJobData
{
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $webexHostId;
	
	/**
	 * @var int
	 */
	protected $dropFolderId;

	
	public function getDescription ()
	{
		return $this->description;
	}
	
	public function setDescription ($v)
	{
		$this->description = $v;
	}
	
	public function getWebexHostId ()
	{
		return $this->webexHostId;
	}
	
	public function setWebexHostId ($v)
	{
		$this->webexHostId = $v;
	}
	
	public function getMetadataProfileId ()
	{
		return $this->metadataProfileId;
	}
	
	public function setMetadataProfileId ($v)
	{
		$this->metadataProfileId = $v;
	}
	
	public function setWebexHostIdMetadataFieldName ($v)
	{
		$this->webexHostIdMetadataFieldName = $v;
	}
	
	public function getWebexHostIdMetadataFieldName ()
	{
		return $this->webexHostIdMetadataFieldName;
	}
	
	public function setCategoriesIdsMetadataFieldName ($v)
	{
		$this->categoriesIdsMetadataFieldName = $v;
	}
	
	public function getCategoriesIdsMetadataFieldName ()
	{
		return $this->categoriesIdsMetadataFieldName;
	}
	
	public function getDropFolderId ()
	{
		return $this->dropFolderId;
	}
	
	public function setDropFolderId ($v)
	{
		$this->dropFolderId = $v;
	}
	
	public function setData (DropFolder $folder, DropFolderFile $dropFolderFileForObject, $dropFolderFileIds)
	{
		parent::setData ($folder, $dropFolderFileForObject, $dropFolderFileIds);
		$this->dropFolderId = $folder->getId();
	}
}
