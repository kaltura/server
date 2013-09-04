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
	protected $metadataProfileId;
	
	/**
	 * @var string
	 */
	protected $webexHostIdMetadataFieldName;
	
	/**
	 * @var string
	 */
	protected $categoriesIdsMetadataFieldName;

	
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
	
	public function setContent (DropFolder $folder, DropFolderFile $dropFolderFileForObject, $dropFolderFileIds)
	{
		parent::setContent($folder, $dropFolderFileForObject, $dropFolderFileIds);
		$this->description = $dropFolderFileForObject->getDescription();
		$this->webexHostId = $dropFolderFileForObject->getWebexHostId();
		$this->metadataProfileId = $folder->getMetadataProfileId();
		$this->webexHostIdMetadataFieldName = $folder->getWebexHostIdMetadataFieldName();
		$this->categoriesIdsMetadataFieldName = $folder->getCategoriesMetadataFieldName();
	}
}
